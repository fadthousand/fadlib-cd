<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Borrowing;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EpubDownloadController extends Controller
{
    public function download(Request $request, Book $book): StreamedResponse
    {
        $user = $request->user();
        if ($user === null) {
            abort(403);
        }

        if (! $user->isStaffOrAdmin()) {
            $member = $user->member;
            $hasActiveLoan = $member !== null && Borrowing::query()
                ->where('member_id', $member->id)
                ->where('status', 'borrowed')
                ->whereHas('details', fn ($query) => $query->where('book_id', $book->id))
                ->exists();

            if (! $hasActiveLoan) {
                abort(403, 'Borrow this volume before downloading its EPUB file.');
            }
        }

        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk(config('filesystems.epub_disk'));

        if ($book->epub_path === null || ! $disk->exists($book->epub_path)) {
            abort(404, 'EPUB archive file not found on storage.');
        }

        $seriesName = $book->category?->name ?? 'Archive';
        $safeFilename = Str::of("{$seriesName} - {$book->title}")
            ->replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], '-')
            ->trim()
            ->append('.epub')
            ->toString();

        return $disk->download($book->epub_path, $safeFilename);
    }
}
