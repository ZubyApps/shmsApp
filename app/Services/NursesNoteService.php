<?php

declare(strict_types = 1);

namespace App\Services;

use App\Models\NursesNote;
use App\Models\User;
use Illuminate\Http\Request;

Class NursesNoteService
{
    public function __construct(private readonly NursesNote $nursesNote)
    {
        
    }

    public function create(Request $data, User $user): NursesNote
    {
       $nursesNote = $user->nursesNote->update([
            'note'         => $data->note,
            'visit_id'     => $data->visitId
        ]);

        return $nursesNote;
    }

    public function update(Request $data, NursesNote $nursesNote, User $user): NursesNote
    {
       $nursesNote->update([
            'note'         => $data->note,
            'visit_id'     => $data->visitId,
            'user_id'      => $user->id
        ]);

        return $nursesNote;
    }
}