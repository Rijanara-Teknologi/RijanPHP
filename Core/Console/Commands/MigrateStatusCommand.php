<?php

namespace Teguh02\Rijanphp\Core\Console\Commands;

use Teguh02\Rijanphp\Core\Database\Migration\Migrator;

class MigrateStatusCommand
{
    protected $basePath;

    public function __construct(string $basePath)
    {
        $this->basePath = $basePath;
    }

    public function handle(array $args)
    {
        echo "\n\033[1mMigration Status\033[0m\n\n";

        $migrator = new Migrator();
        $status = $migrator->status();

        if (empty($status)) {
            echo "\033[33mNo migrations found.\033[0m\n";
            return;
        }

        $headers = ['Migration', 'Status', 'Batch'];
        $rows = [];

        foreach ($status as $item) {
            $rows[] = [
                $item['name'],
                $item['migrated'] ? "\033[32mMigrated\033[0m" : "\033[31mPending\033[0m",
                $item['batch'] ?? '-',
            ];
        }

        $this->table($headers, $rows);
        echo "\n";
    }

    protected function table(array $headers, array $rows)
    {
        $widths = array_map('strlen', $headers);

        foreach ($rows as $row) {
            foreach ($row as $i => $cell) {
                $cleanCell = preg_replace('/\033\[[0-9;]*m/', '', $cell);
                $widths[$i] = max($widths[$i] ?? 0, strlen($cleanCell));
            }
        }

        $separator = $this->buildSeparator($widths);

        echo $separator . "\n";
        echo $this->buildRow($headers, $widths) . "\n";
        echo $separator . "\n";

        foreach ($rows as $row) {
            echo $this->buildRow($row, $widths) . "\n";
        }

        echo $separator . "\n";
    }

    protected function buildRow(array $cells, array $widths): string
    {
        $parts = [];
        foreach ($cells as $i => $cell) {
            $parts[] = ' ' . str_pad($cell, $widths[$i] + (strlen($cell) - strlen(preg_replace('/\033\[[0-9;]*m/', '', $cell)))) . ' ';
        }
        return '|' . implode('|', $parts) . '|';
    }

    protected function buildSeparator(array $widths): string
    {
        $parts = [];
        foreach ($widths as $width) {
            $parts[] = str_repeat('-', $width + 2);
        }
        return '+' . implode('+', $parts) . '+';
    }
}
