<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Crm\CrmPitchDeck;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class PitchDeckController extends Controller
{
    public function index()
    {
        $pageTitle = 'Pitch Decks';
        $staff     = Auth::guard('crm')->user();
        $query     = CrmPitchDeck::with('uploader')->orderByDesc('id');

        if (!$staff->isSuper() && !$staff->hasPermission('crm.pitch_decks.manage')) {
            $portal = $staff->portal();
            $query->where('status', 1)->where(function ($q) use ($portal) {
                $q->where('audience', 'all')->orWhere('audience', $portal);
            });
        }

        $decks = $query->paginate(getPaginate());

        return view('crm.pitch_decks.index', compact('pageTitle', 'decks', 'staff'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:160',
            'description' => 'nullable|string|max:2000',
            'audience'    => 'required|in:all,agent,manager,trader,finance',
            'file'        => ['required', 'file', 'max:20480', new FileTypeValidate(['pdf', 'ppt', 'pptx', 'doc', 'docx'])],
        ]);

        $directory = assetFilesystemPath(getFilePath('crmPitchDeck'));

        try {
            $filename = fileUploader($request->file('file'), $directory);
        } catch (\Exception $e) {
            $notify[] = ['error', 'Could not upload pitch deck.'];
            return back()->withNotify($notify);
        }

        CrmPitchDeck::create([
            'title'         => $request->title,
            'description'   => $request->description,
            'audience'      => $request->audience,
            'file_path'     => $filename,
            'original_name' => $request->file('file')->getClientOriginalName(),
            'uploaded_by'   => Auth::guard('crm')->id(),
            'status'        => 1,
        ]);

        $notify[] = ['success', 'Pitch deck uploaded'];
        return back()->withNotify($notify);
    }

    public function download($id)
    {
        $deck = CrmPitchDeck::findOrFail($id);
        $path = assetFilesystemPath(getFilePath('crmPitchDeck') . '/' . $deck->file_path);

        if (!File::exists($path)) {
            abort(404);
        }

        return response()->download($path, $deck->original_name ?: basename($deck->file_path));
    }

    public function delete($id)
    {
        $deck = CrmPitchDeck::findOrFail($id);
        $path = assetFilesystemPath(getFilePath('crmPitchDeck') . '/' . $deck->file_path);

        if (File::exists($path)) {
            File::delete($path);
        }

        $deck->delete();

        $notify[] = ['success', 'Pitch deck deleted'];
        return back()->withNotify($notify);
    }
}
