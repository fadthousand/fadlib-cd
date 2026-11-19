<?php

namespace App\Console\Commands;

use App\Models\Book;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class SyncEpubsToR2 extends Command
{
    protected $signature = 'epubs:sync-r2 {--force : Timpa berkas yang sudah ada di R2}';

    protected $description = 'Unggah seluruh berkas EPUB lokal ke Cloudflare R2 sesuai metadata database';

    public function handle(): int
    {
        $books = Book::query()
            ->whereNotNull('epub_path')
            ->orderBy('volume_code')
            ->get();

        if ($books->isEmpty()) {
            $this->warn('Tidak ada record buku dengan epub_path di database.');
            return self::FAILURE;
        }

        $local = Storage::disk('local');
        $r2 = Storage::disk('r2');

        $uploaded = 0;
        $skipped = 0;
        $missing = 0;

        foreach ($books as $book) {
            $path = $book->epub_path;

            if (! $local->exists($path)) {
                $this->error("[MISSING] {$book->volume_code}: {$path} tidak ditemukan di disk lokal.");
                $missing++;
                continue;
            }

            if (! $this->option('force') && $r2->exists($path)) {
                $this->line("[SKIP] {$book->volume_code}: sudah ada di R2.");
                $skipped++;
                continue;
            }

            $stream = $local->readStream($path);
            $r2->put($path, $stream);

            if (is_resource($stream)) {
                fclose($stream);
            }

            $this->info("[UPLOADED] {$book->volume_code} -> {$path}");
            $uploaded++;
        }

        $this->newLine();
        $this->info("Selesai. Terunggah: {$uploaded}, Dilewati: {$skipped}, Hilang: {$missing}.");

        return $missing > 0 ? self::FAILURE : self::SUCCESS;
    }
}
