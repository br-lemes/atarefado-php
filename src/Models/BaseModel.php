<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

abstract class BaseModel extends Model
{
    public $timestamps = true;

    public function scopeOrderMap($query, $queryParams, $fieldMap)
    {
        if (!isset($queryParams['orderBy'])) {
            if (isset($fieldMap['id'])) {
                $query->orderBy($fieldMap['id'], 'desc');
            }
            return $query;
        }
        $orderBy = $queryParams['orderBy'];
        if (is_array($orderBy)) {
            foreach ($orderBy as $order) {
                $order = explode(':', $order);
                $field = $order[0];
                $order = $order[1] ?? 'asc';
                if (isset($fieldMap[$field])) {
                    $query->orderBy($fieldMap[$field], $order);
                }
            }
        } else {
            $orderBy = explode(':', $orderBy);
            $field = $orderBy[0];
            $order = $orderBy[1] ?? 'asc';
            if (isset($fieldMap[$field])) {
                $query->orderBy($fieldMap[$field], $order);
            }
        }
        return $query;
    }

    public function scopeWhereMap($query, $queryParams, $fieldMap)
    {
        foreach ($fieldMap as $field => $column) {
            if (!isset($queryParams[$field])) {
                continue;
            }
            if (is_array($queryParams[$field])) {
                $query->whereIn($column, $queryParams[$field]);
            } else {
                $query->where($column, $queryParams[$field]);
            }
        }
        return $query;
    }
}
