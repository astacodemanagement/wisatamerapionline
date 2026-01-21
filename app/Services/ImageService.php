<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;

class ImageService
{
    /**
     * Upload dan konversi gambar ke format WebP (jika gambar),
     * atau simpan file PDF apa adanya.
     *
     * @param UploadedFile|null $file
     * @param string $destinationPath
     * @param string|null $oldFile
     * @return string|null
     * @throws \Exception
     */
    public function handleImageUpload(?UploadedFile $file, string $destinationPath, ?string $oldFile = null): ?string
    {
        if (!$file) {
            return '';
        }

        // Pastikan path diakhiri dengan slash
        $destinationPath = rtrim($destinationPath, '/') . '/';

        // Hapus file lama jika ada
        if ($oldFile && file_exists(public_path($destinationPath . $oldFile))) {
            unlink(public_path($destinationPath . $oldFile));
        }

        // Cek dan buat direktori jika belum ada
        if (!file_exists(public_path($destinationPath))) {
            mkdir(public_path($destinationPath), 0755, true);
        }

        // Validasi tipe MIME
        $fileMimeType = $file->getMimeType();

        if (strpos($fileMimeType, 'image/') === 0) {
            // Ini file gambar, lakukan konversi ke WebP
            return $this->convertImageToWebp($file, $destinationPath, $fileMimeType);
        } elseif ($fileMimeType === 'application/pdf') {
            // Ini file PDF, cukup upload apa adanya
            $originalFileName = $file->getClientOriginalName();
            $fileName = date('YmdHis') . '_' . str_replace(' ', '_', $originalFileName);
            $file->move(public_path($destinationPath), $fileName);
            return $fileName;
        } else {
            throw new \Exception('Format file tidak didukung.');
        }
    }

    /**
     * Konversi file gambar ke WebP
     *
     * @param UploadedFile $file
     * @param string $destinationPath
     * @param string $mimeType
     * @return string
     * @throws \Exception
     */
    private function convertImageToWebp(UploadedFile $file, string $destinationPath, string $mimeType): string
    {
        $originalFileName = $file->getClientOriginalName();
        $imageName = date('YmdHis') . '_' . str_replace(' ', '_', $originalFileName);

        // Simpan gambar original dulu
        $file->move(public_path($destinationPath), $imageName);

        $sourceImagePath = public_path($destinationPath . $imageName);
        $webpImagePath = public_path($destinationPath . pathinfo($imageName, PATHINFO_FILENAME) . '.webp');

        try {
            $sourceImage = match ($mimeType) {
                'image/jpeg' => imagecreatefromjpeg($sourceImagePath),
                'image/png' => imagecreatefrompng($sourceImagePath),
                default => throw new \Exception('Format gambar tidak didukung untuk konversi.')
            };

            if ($sourceImage) {
                imagewebp($sourceImage, $webpImagePath);
                imagedestroy($sourceImage);
                unlink($sourceImagePath);

                return pathinfo($imageName, PATHINFO_FILENAME) . '.webp';
            }
        } catch (\Exception $e) {
            if (file_exists($sourceImagePath)) {
                unlink($sourceImagePath);
            }
            if (file_exists($webpImagePath)) {
                unlink($webpImagePath);
            }
            throw $e;
        }

        return '';
    }
}
