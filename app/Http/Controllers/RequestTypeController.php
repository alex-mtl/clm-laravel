<?php

namespace App\Http\Controllers;

use App\Models\RequestType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RequestTypeController extends Controller
{
    /**
     * Display a listing of request types.
     */
    public function index()
    {
        $cols = collect([
            [
                'name' => 'Название',
                'class' => 'w-10',
                'prop' => 'name',

            ],
            [
                'name' => 'Код',
                'class' => 'w-10',
                'prop' => 'slug',
            ],
            [
                'name' => 'Действия',
                'class' => 'w-10',
                'prop' => 'actions'
            ],
        ])->map(fn($item) => (object)$item);

        return view('request-types.index', [
            'cols' => $cols,
            'requestTypes' => RequestType::orderBy('name')->get()
        ]);
    }

    /**
     * Show the form for creating a new request type.
     */
    public function create()
    {
        $layout = request()->header('X-Ajax-Request') ? 'layouts.ajax' : 'layouts.app';

        return view('request-types.form', [
            'layout' => $layout,
            'mode' => 'create',
            'requestType' => new RequestType(),
            'predefinedTypes' => RequestType::getAvailableTypes()
        ]);
    }

    /**
     * Store a newly created request type.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'slug' => [
                'required',
                'alpha_dash',
                'unique:request_types,slug',
                'max:50',
//                Rule::in(array_keys(RequestType::TYPES)) // Only allow predefined types
            ],
            'name' => 'required|string|max:100',
            'config' => 'nullable|json'
        ]);

        RequestType::create($validated);

        return redirect()->route('request-types.index')
            ->with('success', 'Request type created successfully');
    }

    /**
     * Display the specified request type.
     */
    public function show(RequestType $requestType)
    {
        $layout = request()->header('X-Ajax-Request') ? 'layouts.ajax' : 'layouts.app';

        return view('request-types.form', [
            'requestType' => $requestType,
            'layout' => $layout,
            'mode' => 'show',
        ]);
    }

    /**
     * Show the form for editing the specified request type.
     */
    public function edit(RequestType $requestType)
    {
        $layout = request()->header('X-Ajax-Request') ? 'layouts.ajax' : 'layouts.app';

        return view('request-types.form', [
            'requestType' => $requestType,
            'layout' => $layout,
            'mode' => 'edit',
        ]);
    }

    /**
     * Update the specified request type.
     */
    public function update(Request $request, RequestType $requestType)
    {
        $validated = $request->validate([
            'slug' => [
                'required',
                'alpha_dash',
                'max:50',
                Rule::unique('request_types')->ignore($requestType->id),
                Rule::in(array_keys(RequestType::TYPES))
            ],
            'name' => 'required|string|max:100',
            'config' => 'nullable|json'
        ]);

        $requestType->update($validated);

        return redirect()->route('request-types.index')
            ->with('success', 'Request type updated successfully');
    }

    /**
     * Remove the specified request type.
     */
    public function destroy(RequestType $requestType)
    {
        if ($requestType->requests()->exists()) {
            return back()->with('error',
                'Cannot delete - this type has associated requests');
        }

        $requestType->delete();

        return redirect()->route('request-types.index')
            ->with('success', 'Request type deleted successfully');
    }
}
