<?php

namespace App\Filament\Resources\SongResource\Pages;

use App\Filament\Resources\SongResource;
use App\Models\Album;
use App\Models\Artist;
use App\Models\Song;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Owenoj\LaravelGetId3\GetId3;

class CreateSong extends CreateRecord
{
    protected static string $resource = SongResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return static::getModel()::create($data);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['name'] = 'Song-' . Str::random(7);
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        $song = Song::with(['media'])->find($this->record->id);

        $songMedia = $song->getFirstMedia('songs');



        $track = GetId3::fromDiskAndPath('songs', $songMedia->id . '/' . $songMedia->file_name);


        if ($track->getTitle()) {
            $song->name = $track->getTitle();
        }

        if ($track->getArtist() && $track->getAlbum()) {
            $artist = Artist::firstOrCreate([
                'name' => $track->getArtist()
            ]);

            $album = Album::firstOrCreate([
                'name' => $track->getAlbum(),
                'artist_id' => $artist->id,
            ]);

            $song->album_id = $album->id;
        }




        $song->save();

        if ($track->getArtwork()) {
            $song->addImage($track->getArtwork(true));
        }
    }
}
