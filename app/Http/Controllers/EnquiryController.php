<?php

namespace App\Http\Controllers;

use App\Models\Enquiry;
use App\Support\Content;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class EnquiryController extends Controller
{
    public function store(Request $request, string $type): RedirectResponse
    {
        abort_unless(isset(Enquiry::TYPES[$type]), 404);

        $back = url()->previous(route('contact')).'#form-'.$type;

        // Honeypot: bots fill the hidden "website" field. Pretend it worked.
        if ($request->filled('website')) {
            return redirect()->to($back)->with('enquiry_success', ['type' => $type, 'reference' => null]);
        }

        $validator = Validator::make($request->all(), $this->rules($type), [
            'needs.required' => 'Choose at least one thing you would like us to do.',
            'event_date.after' => 'Choose a date in the future.',
            'attachment.required' => 'Please attach your resume.',
        ], [
            'needs' => 'services',
            'heard' => 'how you heard about us',
        ]);

        if ($validator->fails()) {
            return redirect()->to($back)->withErrors($validator, $type)->withInput($request->except('attachment'));
        }

        $data = $validator->validated();

        if (array_key_exists('date_flexible', $data)) {
            $data['date_flexible'] = $request->boolean('date_flexible') ? 'Yes' : null;
        }

        $attributes = [
            'type' => $type,
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'summary' => $this->summary($type, $data),
            'data' => Arr::except($data, ['name', 'email', 'phone', 'attachment']),
        ];

        if ($file = $request->file('attachment')) {
            $attributes['attachment_path'] = $file->store('enquiries');
            $attributes['attachment_name'] = $file->getClientOriginalName();
        }

        $enquiry = Enquiry::create($attributes);

        return redirect()->to($back)->with('enquiry_success', [
            'type' => $type,
            'reference' => $enquiry->reference,
            'email' => $enquiry->email,
        ]);
    }

    protected function rules(string $type): array
    {
        $maxUpload = 'max:'.(Content::uploadLimitMb() * 1024);
        $person = [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:160'],
        ];
        $phone = ['required', 'string', 'min:7', 'max:40'];

        return match ($type) {
            'quote' => $person + [
                'phone' => $phone,
                'event_type' => ['required', Rule::in(config('site.event_types'))],
                'event_date' => ['nullable', 'date', 'after:today'],
                'date_flexible' => ['nullable', 'boolean'],
                'venue' => ['nullable', 'string', 'max:160'],
                'guests' => ['required', Rule::in(config('site.guest_ranges'))],
                'needs' => ['required', 'array', 'min:1'],
                'needs.*' => [Rule::in(config('site.needs'))],
                'vision' => ['nullable', 'string', 'max:4000'],
                'contact_method' => ['required', Rule::in(config('site.contact_methods'))],
                'heard' => ['nullable', Rule::in(config('site.heard'))],
                'attachment' => ['nullable', 'file', $maxUpload, 'mimes:jpg,jpeg,png,webp,pdf'],
            ],
            'rental' => $person + [
                'phone' => $phone,
                'event_date' => ['nullable', 'date', 'after:today'],
                'items' => ['required', 'string', 'min:3', 'max:1000'],
                'message' => ['nullable', 'string', 'max:4000'],
            ],
            'contact' => $person + [
                'phone' => ['nullable', 'string', 'max:40'],
                'topic' => ['nullable', Rule::in(config('site.topics'))],
                'message' => ['required', 'string', 'min:5', 'max:4000'],
            ],
            'career' => $person + [
                'phone' => $phone,
                'role' => ['required', 'string', 'max:160'],
                'message' => ['nullable', 'string', 'max:4000'],
                'attachment' => ['required', 'file', $maxUpload, 'mimes:pdf,doc,docx'],
            ],
        };
    }

    protected function summary(string $type, array $data): string
    {
        return match ($type) {
            'quote' => $data['event_type'].' · '.(empty($data['event_date']) ? 'Date to be confirmed' : Carbon::parse($data['event_date'])->format('j M Y')),
            'rental' => 'Rental · '.Str::limit($data['items'], 60),
            'contact' => $data['topic'] ?? 'General enquiry',
            'career' => $data['role'],
        };
    }
}
