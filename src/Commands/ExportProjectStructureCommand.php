<?php

namespace Makuruwan\ExportProjectStructure\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Console\Input\InputOption;

class ExportProjectStructureCommand extends Command
{
    protected $name = 'code:export';

    protected $description = 'Export project folders into text files inside the exports directory';

    protected function getTargets(): array
    {
        return config('export-project-structure.targets', []);
    }

    protected function configure(): void
    {
        parent::configure();

        $this->addOption(
            'all',
            null,
            InputOption::VALUE_NONE,
            'Export all configured sections into exports/all.txt'
        );

        $this->addOption(
            'all-separate',
            null,
            InputOption::VALUE_NONE,
            'Export all configured sections into separate files'
        );

        foreach (array_keys($this->getTargets()) as $target) {
            $this->addOption(
                $target,
                null,
                InputOption::VALUE_NONE,
                'Export ' . $target . ' only'
            );
        }
    }

    public function handle(): int
    {
        $exportsDirName = config('export-project-structure.exports_directory', 'exports');
        $exportsDir = base_path($exportsDirName);

        if (!File::exists($exportsDir)) {
            File::makeDirectory($exportsDir, 0755, true);
        }

        if ($this->option('all')) {
            $result = $this->exportAllIntoSingleFile($this->getTargets(), $exportsDir);
            $this->printAvailableCommands();
            return $result;
        }

        if ($this->option('all-separate')) {
            $result = $this->exportAllSeparately($exportsDir);
            $this->printAvailableCommands();
            return $result;
        }

        $selectedTargets = $this->getSelectedTargets();

        if (empty($selectedTargets)) {
            $this->warn('No export option selected.');
            $this->printAvailableCommands();
            return 1;
        }

        foreach ($selectedTargets as $name => $relativePath) {
            $this->exportSingleTarget($name, $relativePath, $exportsDir);
        }

        $this->info('Export completed.');
        $this->printAvailableCommands();

        return 0;
    }

    protected function getSelectedTargets(): array
    {
        $selected = [];

        foreach ($this->getTargets() as $name => $path) {
            if ($this->option($name)) {
                $selected[$name] = $path;
            }
        }

        return $selected;
    }

    protected function exportAllSeparately(string $exportsDir): int
    {
        $exportedAny = false;

        foreach ($this->getTargets() as $name => $relativePath) {
            $didExport = $this->exportSingleTarget($name, $relativePath, $exportsDir);

            if ($didExport) {
                $exportedAny = true;
            }
        }

        if (!$exportedAny) {
            $this->warn('Nothing was exported.');
            return 1;
        }

        $this->info('All separate exports completed.');

        return 0;
    }

    protected function exportAllIntoSingleFile(array $targets, string $exportsDir): int
    {
        $combinedContent = '';
        $exportedAny = false;

        foreach ($targets as $name => $relativePath) {
            $directory = base_path($relativePath);

            if (!File::exists($directory)) {
                $this->warn("Skipped {$name}: directory not found ({$relativePath})");
                continue;
            }

            $files = collect(File::allFiles($directory))
                ->sortBy(fn($file) => str_replace('\\', '/', $file->getPathname()))
                ->values()
                ->all();

            if (empty($files)) {
                $this->warn("Skipped {$name}: no files found");
                continue;
            }

            $exportedAny = true;

            $combinedContent .= "\n\n";
            $combinedContent .= str_repeat('=', 80) . "\n";
            $combinedContent .= strtoupper($name) . "\n";
            $combinedContent .= str_repeat('=', 80) . "\n";

            foreach ($files as $file) {
                $combinedContent .= $this->formatFileContent($file);
            }
        }

        if (!$exportedAny) {
            $this->warn('Nothing was exported.');
            return 1;
        }

        $outputFile = $exportsDir . DIRECTORY_SEPARATOR . 'all.txt';
        File::put($outputFile, ltrim($combinedContent));

        $this->info('Exported all sections -> ' . basename($exportsDir) . '/all.txt');

        return 0;
    }

    protected function exportSingleTarget(string $name, string $relativePath, string $exportsDir): bool
    {
        $directory = base_path($relativePath);

        if (!File::exists($directory)) {
            $this->warn("Skipped {$name}: directory not found ({$relativePath})");
            return false;
        }

        $files = collect(File::allFiles($directory))
            ->sortBy(fn($file) => str_replace('\\', '/', $file->getPathname()))
            ->values()
            ->all();

        if (empty($files)) {
            $this->warn("Skipped {$name}: no files found");
            return false;
        }

        $combinedContent = '';

        foreach ($files as $file) {
            $combinedContent .= $this->formatFileContent($file);
        }

        $outputFile = $exportsDir . DIRECTORY_SEPARATOR . "all-{$name}.txt";
        File::put($outputFile, ltrim($combinedContent));

        $this->info("Exported {$name} -> " . basename($exportsDir) . "/all-{$name}.txt");

        return true;
    }

    protected function formatFileContent(\SplFileInfo $file): string
    {
        $content = File::get($file->getPathname());
        $relativeFilePath = str_replace(base_path() . DIRECTORY_SEPARATOR, '', $file->getPathname());

        if (strtolower($file->getExtension()) === 'php') {
            $content = preg_replace('/<\?php\s*/', '', $content);
            $content = preg_replace('/\?>\s*/', '', $content);
        }

        return "\n\n/* ===== {$relativeFilePath} ===== */\n\n" . trim($content) . "\n";
    }

    protected function printAvailableCommands(): void
    {
        $this->newLine();
        $this->comment('Commands you can run:');
        $this->newLine();

        $this->comment('Export everything into one file:');
        $this->line('  php artisan code:export --all');
        $this->newLine();

        $this->comment('Export everything into separate files:');
        $this->line('  php artisan code:export --all-separate');
        $this->newLine();

        foreach (array_keys($this->getTargets()) as $target) {
            $this->comment('Export only ' . $target . ':');
            $this->line("  php artisan code:export --{$target}");
            $this->newLine();
        }

        $exampleTargets = array_slice(array_keys($this->getTargets()), 0, min(3, count($this->getTargets())));

        if (!empty($exampleTargets)) {
            $exampleCommand = 'php artisan code:export ' . implode(
                ' ',
                array_map(fn($target) => "--{$target}", $exampleTargets)
            );

            $this->comment('Export multiple selected sections:');
            $this->line("  {$exampleCommand}");
            $this->newLine();
        }
    }
}