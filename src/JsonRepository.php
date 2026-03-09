<?php

class JsonRepository {

    private $file;

    public function __construct($file)
    {
        $this->file = $file;

        if (!file_exists($file)) {
            file_put_contents($file, json_encode([], JSON_PRETTY_PRINT));
        }
    }

    private function read()
    {
        $content = file_get_contents($this->file);
        return json_decode($content, true) ?? [];
    }

    private function write($data)
    {
        file_put_contents($this->file, json_encode($data, JSON_PRETTY_PRINT));
    }

    public function all(): array
    {
        return $this->read();
    }

    public function find(int $id): ?array
    {
        foreach ($this->read() as $item) {
            if ($item["id"] == $id) {
                return $item;
            }
        }
        return null;
    }

    public function add(array $item): array
    {
        $data = $this->read();

        $maxId = 0;
        foreach ($data as $row) {
            if ($row["id"] > $maxId) {
                $maxId = $row["id"];
            }
        }

        $item["id"] = $maxId + 1;

        $data[] = $item;

        $this->write($data);

        return $item;
    }

    public function update(int $id, array $fields): bool
    {
        $data = $this->read();

        foreach ($data as &$row) {

            if ($row["id"] == $id) {

                foreach ($fields as $key => $value) {
                    $row[$key] = $value;
                }

                $this->write($data);

                return true;
            }
        }

        return false;
    }

    public function delete(int $id): bool
    {
        $data = $this->read();

        foreach ($data as $index => $row) {

            if ($row["id"] == $id) {

                array_splice($data, $index, 1);

                $this->write($data);

                return true;
            }
        }

        return false;
    }
}