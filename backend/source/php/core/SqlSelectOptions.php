<?php

namespace oml\php\core;

use PDOStatement;

class SqlSelectOptions
{
    public ?int $pageIndex;
    public ?int $pageSize;

    private array $whereList = [];
    private array $orderByList = [];

    public function __construct(int|null $pageIndex = null, int|null $pageSize = null)
    {
        $this->pageIndex = $pageIndex;
        $this->pageSize = $pageSize;
    }

    public function where(array $options)
    {
        // [
        //     "column" => $column,
        //     "operator" => $operator,
        //     "bind" => $bind,
        //     "and" => $and
        // ] = $options;

        // [
        //     "query" => $query,
        //     "binds" => $binds,
        //     "and" => $and
        // ] = $options;

        $this->whereList[] = $options;
    }

    public function getWhereQuery(): string
    {
        $buffer = "";

        if (count($this->whereList) > 0) {
            $buffer = "WHERE ";

            foreach ($this->whereList as $index => $where) {
                if ($index !== 0) {
                    $buffer .= ($where["and"] ? " AND " : " OR ");
                }

                if (count($where) === 5) {
                    [
                        "column" => $column,
                        "operator" => $operator
                    ] = $where;

                    $buffer .= "{$column} {$operator} :" . ___UNIQUE_SYMBOL___ . "_{$index}";
                }

                if (count($where) === 3) {
                    $buffer .= $where["query"];
                }
            }
        }

        return $buffer;
    }

    public function applyWhereBinds(PDOStatement $statement)
    {
        foreach ($this->whereList as $index => $where) {
            if (count($where) === 5) {
                $statement->bindValue(___UNIQUE_SYMBOL___ . "_{$index}", ...$where["bind"]);
            }

            if (count($where) === 3) {
                foreach ($where["binds"] as $bind) {
                    $statement->bindValue(...$bind);
                }
            }
        }
    }

    public function orderBy(string $column, string $order = 'ASC')
    {
        $this->orderByList[] = [$column, $order];
    }

    public function getOrderByQuery(): string
    {
        $q = "";
        $c = count($this->orderByList);

        if ($c > 0) {
            $q = "ORDER BY ";

            foreach ($this->orderByList as $i => $o) {
                $q .= "{$o[0]} {$o[1]}";

                if ($i < $c - 1) {
                    $q .= ", ";
                }
            }
        }

        return $q;
    }

    public function getLimitAndOffset(): string
    {
        if ($this->pageIndex !== null && $this->pageSize !== null) {
            $offset = $this->pageSize * ($this->pageIndex - 1);
            return "LIMIT {$this->pageSize} OFFSET {$offset}";
        } else {
            return "";
        }
    }
}
