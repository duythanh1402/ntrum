<?php

namespace VietQR\Base;

abstract class BaseQuery
{
    /**
     * Table name.
     * 
     * @var string
     */
    protected static $table_name;

    /**
     * Primary key column name.
     * 
     * @var string
     */
    protected static $primary_key = 'id';

    /**
     * Field mapping from data keys to column names.
     * 
     * @var array
     */
    protected static $field_mapping = [];

    /**
     * Apply field mapping to data array and serialize data if needed.
     *
     * @param array $data
     * @return array
     */
    protected static function apply_field_mapping($data)
    {
        $mapped_data = [];
        foreach ($data as $key => $value) {
            $column_name = static::$field_mapping[$key] ?? $key;
            if (is_array($value)) {
                $mapped_data[$column_name] = serialize($value);
            } else {
                $mapped_data[$column_name] = $value;
            }
        }
        return $mapped_data;
    }

    /**
     * Unserialize data if needed.
     *
     * @param array $data
     * @return array
     */
    protected static function unapply_field_mapping($data)
    {
        $unmapped_data = [];
        foreach ($data as $key => $value) {
            $column_name = array_search($key, static::$field_mapping);
            if ($column_name !== false) {
                $unmapped_data[$column_name] = $value;
            } else {
                $unmapped_data[$key] = $value;
            }
            if (is_serialized($value)) {
                $unmapped_data[$key] = unserialize($value);
            }
        }
        return $unmapped_data;
    }

    /**
     * Get all records from the table.
     *
     * @return array
     */
    public static function get_all()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . static::$table_name;

        $query = "SELECT * FROM $table_name";
        $results = $wpdb->get_results($query, ARRAY_A);
        $unmapped_results = [];
        foreach ($results as $result) {
            $unmapped_results[] = self::unapply_field_mapping((array)$result);
        }

        return $unmapped_results;
    }

    /**
     * Get a record by primary key.
     *
     * @param int $id
     * @return object|null
     */
    public static function get_by_id($id)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . static::$table_name;

        $query = $wpdb->prepare("SELECT * FROM $table_name WHERE " . static::$primary_key . " = %d", $id);
        $result = $wpdb->get_row($query, ARRAY_A);
        if ($result) {
            return self::unapply_field_mapping((array)$result);
        }
        return null;
    }

    /**
     * Insert a new record.
     *
     * @param array $data
     * @return int|false
     */
    public static function insert($data)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . static::$table_name;
        $mapped_data = self::apply_field_mapping($data);

        return $wpdb->insert($table_name, $mapped_data);
    }

    /**
     * Update a record.
     *
     * @param int $id primary key value
     * @param array $data
     * @return int|false
     */
    public static function update($id, $data)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . static::$table_name;
        $mapped_data = self::apply_field_mapping($data);
        return $wpdb->update($table_name, $mapped_data, [static::$primary_key => $id]);
    }

    /**
     * Update a record by condition.
     *
     * @param array $data
     * @param array $conditions
     * @return int|false
     */
    public static function update_where($data, $conditions = null)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . static::$table_name;

        // Make set clause
        $mapped_data = self::apply_field_mapping($data);
        ['set_clause' => $set_clause, 'set_values' => $set_values] 
            = self::make_set_clause($mapped_data);

        // Make where clause
        $mapped_conditions = self::apply_field_mapping($conditions);
        ['where_clause' => $where_clause, 'where_values' => $where_values] 
            = self::make_where_clause($mapped_conditions);

        $query = $wpdb->prepare(
            "UPDATE $table_name 
            SET $set_clause 
            WHERE $where_clause",
            array_merge($set_values, $where_values)
        );
        return $wpdb->query($query);
    }

    /**
     * Make where clause from conditions. 
     * 
     * @param array $conditions Already mapped conditions
     * @return array ['where_clause' => string, 'where_values' => array]
     */
    protected static function make_where_clause($conditions)
    {
        $where_conditions = [];
        $where_conditions[] = "1 = 1"; // Default condition
        $where_values = [];
        foreach ($conditions as $field => $value) {
            if (is_null($value)) {
                $where_conditions[] = "$field = NULL";
            } elseif (is_int($value)) {
                $where_conditions[] = "$field = %d";
            } else {
                $where_conditions[] = "$field = %s";
            }
            $where_values[] = $value;
        }
        $where_clause = implode(' AND ', $where_conditions);

        return [
            'where_clause' => $where_clause,
            'where_values' => $where_values,
        ];
    }

    /**
     * Make set clause from data.
     * 
     * @param array $data
     * @return array ['set_clause' => string, 'set_values' => array]
     */
    protected static function make_set_clause($data)
    {
        $set_conditions = [];
        $set_values = [];
        foreach ($data as $field => $value) {
            if (is_null($value)) {
                $set_conditions[] = "$field = NULL";
            } elseif (is_int($value)) {
                $set_conditions[] = "$field = %d";
            } else {
                $set_conditions[] = "$field = %s";
            }
            $set_values[] = $value;
        }
        $set_clause = implode(', ', $set_conditions);

        return [
            'set_clause' => $set_clause,
            'set_values' => $set_values,
        ];
    }

    /**
     * Delete a record.
     *
     * @param int $id primary key value
     * @return int|false
     */
    public static function delete($id)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . static::$table_name;

        return $wpdb->delete($table_name, [static::$primary_key => $id]);
    }

    /**
     * Find where
     * 
     * @param array $conditions
     * @return array
     */
    public static function find_where($conditions)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . static::$table_name;
        
        $mapped_conditions = self::apply_field_mapping($conditions);
        ['where_clause' => $where_clause, 'where_values' => $where_values] 
            = self::make_where_clause($mapped_conditions);
        
        $query = $wpdb->prepare("SELECT * FROM $table_name WHERE {$where_clause}", $where_values);
        $results = $wpdb->get_results($query, ARRAY_A);

        
        $unmapped_results = [];
        foreach ($results as $result) {
            $unmapped_results[] = self::unapply_field_mapping((array)$result);
        }

        return $unmapped_results;
    }

    /**
     * Insert multiple records.
     *
     * @param array $data_array Array of data to be inserted
     * @return int|false The number of rows inserted, or false on error
     */
    public static function insert_many($data_array)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . static::$table_name;

        // Validate if this is a valid data array
        if (empty($data_array)) {
            return false;
        }

        foreach ($data_array as $data) {
            if (!is_array($data)) {
                throw new \Exception('Data array is not valid');
            }
        }
        
        // Make into clause
        $into_clause = "(" . implode(', ', array_keys($data_array[0])) . ")";

        // Make value clause
        $values_clause = implode(', ', array_map(function($data) {
            $mapped_data = self::apply_field_mapping($data);
            $placeholders = array_map(function($value) {
                if (is_null($value)) {
                    return 'NULL';
                } elseif (is_int($value)) {
                    return '%d';
                } elseif (is_float($value)) {
                    return '%f';
                } else {
                    return '%s';
                }
            }, $mapped_data);
            return '(' . implode(', ', $placeholders) . ')';
        }, $data_array));

        $query = $wpdb->prepare(
            "INSERT INTO $table_name $into_clause VALUES $values_clause",
            array_merge(...array_map(function($data) {
                return array_values(self::apply_field_mapping($data));
            }, $data_array))
        );

        return $wpdb->query($query);
    }
}