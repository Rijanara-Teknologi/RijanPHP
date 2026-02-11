<?php

namespace Teguh02\Rijanphp\Core\Model;

use Teguh02\Rijanphp\Core\Database\QueryBuilder;

abstract class Model
{
    protected $table;
    protected $primaryKey = 'id';
    protected $returnType = 'array'; // 'array' or 'object'
    protected $useSoftDeletes = false;
    protected $allowedFields = [];
    protected $useTimestamps = false;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;

    // Callbacks
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    protected $db;
    protected $builder;

    public function __construct()
    {
        $this->builder = db();
        $this->db = $this->builder->getConnection();
        $this->builder->table($this->table);
    }

    /**
     * Save a record (Insert if no primary key, Update otherwise).
     */
    public function save($data)
    {
        if (isset($data[$this->primaryKey])) {
            $id = $data[$this->primaryKey];
            unset($data[$this->primaryKey]);
            return $this->update($id, $data);
        }

        return $this->insert($data);
    }

    /**
     * Set the where clause for the next operation.
     */
    public function where($column, $operator = null, $value = null)
    {
        $this->builder->where($column, $operator, $value);
        return $this;
    }

    /**
     * Find a single record by primary key.
     */
    public function find($id = null)
    {
        if ($this->useSoftDeletes) {
            $this->builder->where($this->deletedField, null);
        }

        if ($id) {
            $this->builder->where($this->primaryKey, $id);
        }

        $result = $this->builder->get();
        return $this->formatResult($result[0] ?? null);
    }

    /**
     * Find all records.
     */
    public function findAll()
    {
        if ($this->useSoftDeletes) {
            $this->builder->where($this->deletedField, null);
        }

        $results = $this->builder->get();
        return array_map([$this, 'formatResult'], $results);
    }

    /**
     * Insert a new record.
     */
    public function insert($data)
    {
        if (!$this->skipValidation && !$this->validate($data)) {
            return false;
        }

        if ($this->useTimestamps) {
            $data[$this->createdField] = date('Y-m-d H:i:s');
            $data[$this->updatedField] = date('Y-m-d H:i:s');
        }

        $data = $this->filterAllowedFields($data);

        // Trigger beforeInsert callbacks
        $data = $this->triggerCallbacks('beforeInsert', $data);

        $id = $this->builder->insert($data);

        // Trigger afterInsert callbacks
        $this->triggerCallbacks('afterInsert', ['id' => $id, 'data' => $data]);

        return $id;
    }

    /**
     * Update an existing record.
     */
    public function update($id, $data)
    {
        if (!$this->skipValidation && !$this->validate($data)) {
            return false;
        }

        if ($this->useTimestamps) {
            $data[$this->updatedField] = date('Y-m-d H:i:s');
        }

        $data = $this->filterAllowedFields($data);

        // Trigger beforeUpdate callbacks
        $data = $this->triggerCallbacks('beforeUpdate', $data);

        $this->builder->where($this->primaryKey, $id);
        $result = $this->builder->update($data);

        // Trigger afterUpdate callbacks
        $this->triggerCallbacks('afterUpdate', ['id' => $id, 'data' => $data, 'result' => $result]);

        return $result;
    }

    /**
     * Delete a record.
     */
    public function delete($id)
    {
        $this->builder->where($this->primaryKey, $id);

        // Trigger beforeDelete
        $this->triggerCallbacks('beforeDelete', ['id' => $id]);

        if ($this->useSoftDeletes) {
            $result = $this->builder->update([$this->deletedField => date('Y-m-d H:i:s')]);
        } else {
            $result = $this->builder->delete();
        }

        // Trigger afterDelete
        $this->triggerCallbacks('afterDelete', ['id' => $id, 'result' => $result]);

        return $result;
    }

    /**
     * Basic validation (Placeholder/Simple implementation)
     */
    public function validate($data)
    {
        // For now, let's implement a very simple validation logic.
        // Real CI style would use a separate Validation service.
        return true;
    }

    protected function filterAllowedFields(array $data)
    {
        if (empty($this->allowedFields)) {
            return $data;
        }

        return array_intersect_key($data, array_flip($this->allowedFields));
    }

    protected function formatResult($data)
    {
        if (!$data)
            return null;

        if ($this->returnType === 'object') {
            return (object) $data;
        }

        return $data;
    }

    protected function triggerCallbacks(string $event, $data)
    {
        foreach ($this->{$event} as $method) {
            if (method_exists($this, $method)) {
                $data = $this->{$method}($data);
            }
        }

        return $data;
    }

    /**
     * Proxy for Query Builder methods
     */
    public function select($columns)
    {
        $this->builder->select($columns);
        return $this;
    }

    public function orderBy($column, $direction = 'ASC')
    {
        $this->builder->orderBy($column, $direction);
        return $this;
    }

    public function limit($limit, $offset = null)
    {
        $this->builder->limit($limit, $offset);
        return $this;
    }
}
