<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Http\UploadedFile;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Support\Str;

class Song extends Model implements HasMedia
{
    use InteractsWithMedia;
    public function album(): BelongsTo
    {
        return $this->belongsTo(Album::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('songs')
            ->useDisk('songs')
            ->singleFile();

        $this->addMediaCollection('artworks')
            ->useDisk('artworks')
            ->singleFile();
    }

    public function addImage(UploadedFile $image)
    {
        $filename = Str::random(10) . '.' . $image->getClientOriginalExtension();

        $this->addMedia($image)
            ->usingFileName($filename)
            ->toMediaCollection('artworks');
    }
}
