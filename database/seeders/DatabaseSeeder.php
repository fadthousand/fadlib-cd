<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Category;
use App\Models\Member;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use ZipArchive;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->create([
            'name' => 'Developer',
            'email' => 'admin@flcd.io',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        User::query()->create([
            'name' => 'Librarian',
            'email' => 'staff@flcd.io',
            'password' => Hash::make('staff123'),
            'role' => 'staff',
        ]);

        /** @var User $memberUser */
        $memberUser = User::query()->create([
            'name' => 'Reader',
            'email' => 'member@flcd.io',
            'password' => Hash::make('member123'),
            'role' => 'member',
        ]);

        Member::query()->create([
            'user_id' => $memberUser->id,
            'member_code' => 'MEM-2026-1',
            'full_name' => 'Reader',
            'email' => 'member@flcd.io',
            'phone' => null,
            'address' => null,
            'status' => 'active',
        ]);

        Member::query()->create([
            'user_id' => null,
            'member_code' => 'US-2026-1',
            'full_name' => 'Fad T.',
            'email' => 'fad@flcd.io',
            'phone' => '+1-929-1',
            'address' => '635 4th Ave, Brooklyn, NY 11232, United States',
            'status' => 'active',
        ]);

        Member::query()->create([
            'user_id' => null,
            'member_code' => 'JP-2026-1',
            'full_name' => 'H. Kei',
            'email' => 'kei@flcd.io',
            'phone' => '+81-22-1',
            'address' => '2 Chome-9-28 Ichibancho, Aoba Ward, Sendai, Miyagi 980-0811, Japan',
            'status' => 'active',
        ]);

        $seriesCatalog = [
            [
                'name' => 'Classroom of the Elite — Year 1',
                'slug' => 'classroom-of-the-elite-year-1',
                'author' => 'Shōgo Kinugasa',
                'volumes' => [
                    ['code' => 'COTE-Y1V01', 'title' => 'Volume 01', 'file' => 'cote-y1v01.epub'],
                    ['code' => 'COTE-Y1V02', 'title' => 'Volume 02', 'file' => 'cote-y1v02.epub'],
                    ['code' => 'COTE-Y1V03', 'title' => 'Volume 03', 'file' => 'cote-y1v03.epub'],
                    ['code' => 'COTE-Y1V04', 'title' => 'Volume 04', 'file' => 'cote-y1v04.epub'],
                    ['code' => 'COTE-Y1V04.5', 'title' => 'Volume 04.5', 'file' => 'cote-y1v04-5.epub'],
                    ['code' => 'COTE-Y1V05', 'title' => 'Volume 05', 'file' => 'cote-y1v05.epub'],
                    ['code' => 'COTE-Y1V06', 'title' => 'Volume 06', 'file' => 'cote-y1v06.epub'],
                    ['code' => 'COTE-Y1V07', 'title' => 'Volume 07', 'file' => 'cote-y1v07.epub'],
                    ['code' => 'COTE-Y1V07.5', 'title' => 'Volume 07.5', 'file' => 'cote-y1v07-5.epub'],
                    ['code' => 'COTE-Y1V08', 'title' => 'Volume 08', 'file' => 'cote-y1v08.epub'],
                    ['code' => 'COTE-Y1V09', 'title' => 'Volume 09', 'file' => 'cote-y1v09.epub'],
                    ['code' => 'COTE-Y1V10', 'title' => 'Volume 10', 'file' => 'cote-y1v10.epub'],
                    ['code' => 'COTE-Y1V11', 'title' => 'Volume 11', 'file' => 'cote-y1v11.epub'],
                    ['code' => 'COTE-Y1V11.5', 'title' => 'Volume 11.5', 'file' => 'cote-y1v11-5.epub'],
                ],
            ],
            [
                'name' => 'Classroom of the Elite — Year 2',
                'slug' => 'classroom-of-the-elite-year-2',
                'author' => 'Shōgo Kinugasa',
                'volumes' => [
                    ['code' => 'COTE-Y2V01', 'title' => 'Year 2 — Volume 01', 'file' => 'cote-y2v01.epub'],
                    ['code' => 'COTE-Y2V02', 'title' => 'Year 2 — Volume 02', 'file' => 'cote-y2v02.epub'],
                    ['code' => 'COTE-Y2V03', 'title' => 'Year 2 — Volume 03', 'file' => 'cote-y2v03.epub'],
                    ['code' => 'COTE-Y2V04', 'title' => 'Year 2 — Volume 04', 'file' => 'cote-y2v04.epub'],
                    ['code' => 'COTE-Y2V04.5', 'title' => 'Year 2 — Volume 04.5', 'file' => 'cote-y2v04-5.epub'],
                    ['code' => 'COTE-Y2V05', 'title' => 'Year 2 — Volume 05', 'file' => 'cote-y2v05.epub'],
                    ['code' => 'COTE-Y2V06', 'title' => 'Year 2 — Volume 06', 'file' => 'cote-y2v06.epub'],
                    ['code' => 'COTE-Y2V07', 'title' => 'Year 2 — Volume 07', 'file' => 'cote-y2v07.epub'],
                    ['code' => 'COTE-Y2V08', 'title' => 'Year 2 — Volume 08', 'file' => 'cote-y2v08.epub'],
                    ['code' => 'COTE-Y2V09', 'title' => 'Year 2 — Volume 09', 'file' => 'cote-y2v09.epub'],
                    ['code' => 'COTE-Y2V09.5', 'title' => 'Year 2 — Volume 09.5', 'file' => 'cote-y2v09-5.epub'],
                    ['code' => 'COTE-Y2V10', 'title' => 'Year 2 — Volume 10', 'file' => 'cote-y2v10.epub'],
                    ['code' => 'COTE-Y2V11', 'title' => 'Year 2 — Volume 11', 'file' => 'cote-y2v11.epub'],
                    ['code' => 'COTE-Y2V12', 'title' => 'Year 2 — Volume 12', 'file' => 'cote-y2v12.epub'],
                    ['code' => 'COTE-Y2V12.5', 'title' => 'Year 2 — Volume 12.5', 'file' => 'cote-y2v12-5.epub'],
                ],
            ],
            [
                'name' => 'Josee, the Tiger and the Fish',
                'slug' => 'josee-the-tiger-and-the-fish',
                'author' => 'Seiko Tanabe',
                'volumes' => [
                    ['code' => 'JTTF-01', 'title' => 'Complete Edition', 'file' => 'jttf-01.epub'],
                ],
            ],
            [
                'name' => 'Secrets of the Silent Witch',
                'slug' => 'secrets-of-the-silent-witch',
                'author' => 'Matsuri Isora',
                'volumes' => [
                    ['code' => 'SSW-V01', 'title' => 'Volume 01', 'file' => 'ssw-v01.epub'],
                    ['code' => 'SSW-V02', 'title' => 'Volume 02', 'file' => 'ssw-v02.epub'],
                    ['code' => 'SSW-V03', 'title' => 'Volume 03', 'file' => 'ssw-v03.epub'],
                    ['code' => 'SSW-V04', 'title' => 'Volume 04', 'file' => 'ssw-v04.epub'],
                    ['code' => 'SSW-V04.5', 'title' => 'Volume 04.5', 'file' => 'ssw-v04-5.epub'],
                    ['code' => 'SSW-V05', 'title' => 'Volume 05', 'file' => 'ssw-v05.epub'],
                ],
            ],
            [
                'name' => 'Suzume',
                'slug' => 'suzume',
                'author' => 'Makoto Shinkai',
                'volumes' => [
                    ['code' => 'SZM-01', 'title' => 'Complete Edition', 'file' => 'szm-01.epub'],
                ],
            ],
            [
                'name' => 'The Eminence in Shadow',
                'slug' => 'the-eminence-in-shadow',
                'author' => 'Daisuke Aizawa',
                'volumes' => [
                    ['code' => 'TEIS-V01', 'title' => 'Volume 01', 'file' => 'teis-v01.epub'],
                    ['code' => 'TEIS-V02', 'title' => 'Volume 02', 'file' => 'teis-v02.epub'],
                    ['code' => 'TEIS-V03', 'title' => 'Volume 03', 'file' => 'teis-v03.epub'],
                    ['code' => 'TEIS-V04', 'title' => 'Volume 04', 'file' => 'teis-v04.epub'],
                    ['code' => 'TEIS-V05', 'title' => 'Volume 05', 'file' => 'teis-v05.epub'],
                    ['code' => 'TEIS-V06', 'title' => 'Volume 06', 'file' => 'teis-v06.epub'],
                ],
            ],
            [
                'name' => 'Weathering with You',
                'slug' => 'weathering-with-you',
                'author' => 'Makoto Shinkai',
                'volumes' => [
                    ['code' => 'WWY-01', 'title' => 'Complete Edition', 'file' => 'wwy-01.epub'],
                ],
            ],
        ];

        $copiedCount = 0;
        $fallbackCount = 0;

        foreach ($seriesCatalog as $series) {
            /** @var Category $category */
            $category = Category::query()->create([
                'name' => $series['name'],
                'slug' => $series['slug'],
                'author' => $series['author'],
                'epigraph' => null,
                'description' => null,
            ]);

            foreach ($series['volumes'] as $volume) {
                $fileName = $volume['file'];
                $epubRelativePath = "epubs/{$fileName}";
                $sourcePath = $this->resolveSeedSourcePath($fileName);

                if ($sourcePath !== null) {
                    $contents = file_get_contents($sourcePath);
                    if ($contents === false) {
                        throw new RuntimeException("Unable to read EPUB file: {$sourcePath}");
                    }

                    $metadata = $this->extractEpubMetadata($sourcePath);
                    Storage::disk('local')->put($epubRelativePath, $contents);
                    $copiedCount++;
                } else {
                    $metadata = ['year' => null, 'synopsis' => null];
                    Storage::disk('local')->put(
                        $epubRelativePath,
                        "FADLIB-CD PLACEHOLDER EPUB FOR {$volume['code']} - {$volume['title']}"
                    );
                    $fallbackCount++;
                }

                Book::query()->create([
                    'category_id' => $category->id,
                    'volume_code' => $volume['code'],
                    'title' => $volume['title'],
                    'author' => $series['author'],
                    'publication_year' => $metadata['year'],
                    'synopsis' => $metadata['synopsis'],
                    'curator_note' => null,
                    'epub_path' => $epubRelativePath,
                    'stock' => 10,
                    'total_copies' => 10,
                ]);
            }
        }

        $this->command?->info("Seeded 44 volumes ({$copiedCount} copied from seed-sources, {$fallbackCount} placeholders).");
    }

    private function resolveSeedSourcePath(string $fileName): ?string
        {
            $candidateNames = array_unique([
                $fileName,
                str_replace('-01.epub', '-v01.epub', $fileName),
                str_replace('-01.epub', '.epub', $fileName),
            ]);

            $directories = [
                storage_path('seed-sources'),
                storage_path('seed-sources/epubs'),
            ];

            foreach ($directories as $directory) {
                if (! is_dir($directory)) {
                    continue;
                }

                foreach ($candidateNames as $candidate) {
                    $exactPath = $directory . DIRECTORY_SEPARATOR . $candidate;
                    if (is_file($exactPath)) {
                        return $exactPath;
                    }
                }

                $entries = scandir($directory);
                if ($entries !== false) {
                    $lowercaseTargets = array_map('strtolower', $candidateNames);
                    foreach ($entries as $entry) {
                        if (in_array(strtolower($entry), $lowercaseTargets, true)) {
                            $matchedPath = $directory . DIRECTORY_SEPARATOR . $entry;
                            if (is_file($matchedPath)) {
                                return $matchedPath;
                            }
                        }
                    }
                }
            }

            return null;
        }

    /**
     * @return array{year: int|null, synopsis: string|null}
     */
    private function extractEpubMetadata(string $sourcePath): array
    {
        $year = null;
        $synopsis = null;

        if (! class_exists(ZipArchive::class)) {
            return ['year' => null, 'synopsis' => null];
        }

        $zip = new ZipArchive();
        if ($zip->open($sourcePath) !== true) {
            return ['year' => null, 'synopsis' => null];
        }

        $containerXml = $zip->getFromName('META-INF/container.xml');
        if ($containerXml !== false) {
            $container = @simplexml_load_string($containerXml);
            if ($container !== false) {
                $rootfiles = $container->xpath("//*[local-name()='rootfile']");
                $opfPath = isset($rootfiles[0]['full-path']) ? (string) $rootfiles[0]['full-path'] : null;

                if ($opfPath !== null) {
                    $opfXml = $zip->getFromName($opfPath);
                    if ($opfXml !== false) {
                        $opf = @simplexml_load_string($opfXml);
                        if ($opf !== false) {
                            $dates = $opf->xpath("//*[local-name()='date']");
                            if (! empty($dates)) {
                                $rawDate = trim((string) $dates[0]);
                                if (preg_match('/\b(19|20)\d{2}\b/', $rawDate, $matches) === 1) {
                                    $year = (int) $matches[0];
                                }
                            }

                            $descriptions = $opf->xpath("//*[local-name()='description']");
                            if (! empty($descriptions)) {
                                $rawDescription = html_entity_decode((string) $descriptions[0], ENT_QUOTES | ENT_HTML5, 'UTF-8');
                                $cleanDescription = trim((string) preg_replace('/\s+/', ' ', strip_tags($rawDescription)));
                                if ($cleanDescription !== '') {
                                    $synopsis = $cleanDescription;
                                }
                            }
                        }
                    }
                }
            }
        }

        $zip->close();

        return [
            'year' => $year,
            'synopsis' => $synopsis,
        ];
    }
}
