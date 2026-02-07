<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CustomerService;

class CustomerServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customerServices = CustomerService::all();
        return view('admin.customer_service.index', compact('customerServices'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.customer_service.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'whatsapp_number' => 'required|string|max:20',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'required|boolean',
        ]);

        $data = $request->all();

        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $photoName = time() . '_cs_' . uniqid() . '.webp';
            if (!file_exists(public_path('uploads/customer_service'))) {
                mkdir(public_path('uploads/customer_service'), 0777, true);
            }
            
            // Process image: resize to 300x300 and convert to WebP
            $imageManager = new \Intervention\Image\ImageManager(
                new \Intervention\Image\Drivers\Gd\Driver()
            );
            $image = $imageManager->read($photo->getPathname())
                ->cover(300, 300)
                ->toWebp(80);
            file_put_contents(public_path('uploads/customer_service/' . $photoName), (string) $image);
            $data['photo'] = $photoName;
        }

        CustomerService::create($data);

        return redirect()->route('admin.customer-service.index')
            ->with('success', 'Customer Service berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $customerService = CustomerService::findOrFail($id);
        return view('admin.customer_service.edit', compact('customerService'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'whatsapp_number' => 'required|string|max:20',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'required|boolean',
        ]);

        $customerService = CustomerService::findOrFail($id);
        $data = $request->all();

        if ($request->hasFile('photo')) {
            // Delete old photo
            if ($customerService->photo) {
                $oldPhotoPath = public_path('uploads/customer_service/' . $customerService->photo);
                if (file_exists($oldPhotoPath)) {
                    unlink($oldPhotoPath);
                }
            }

            $photo = $request->file('photo');
            $photoName = time() . '_cs_' . uniqid() . '.webp';
            if (!file_exists(public_path('uploads/customer_service'))) {
                mkdir(public_path('uploads/customer_service'), 0777, true);
            }
            
            // Process image: resize to 300x300 and convert to WebP
            $imageManager = new \Intervention\Image\ImageManager(
                new \Intervention\Image\Drivers\Gd\Driver()
            );
            $image = $imageManager->read($photo->getPathname())
                ->cover(300, 300)
                ->toWebp(80);
            file_put_contents(public_path('uploads/customer_service/' . $photoName), (string) $image);
            $data['photo'] = $photoName;
        }

        $customerService->update($data);

        return redirect()->route('admin.customer-service.index')
            ->with('success', 'Customer Service berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $customerService = CustomerService::findOrFail($id);
        
        // Delete photo file
        if ($customerService->photo) {
            $photoPath = public_path('uploads/customer_service/' . $customerService->photo);
            if (file_exists($photoPath)) {
                unlink($photoPath);
            }
        }

        $customerService->delete();

        return redirect()->route('admin.customer-service.index')
            ->with('success', 'Customer Service berhasil dihapus.');
    }
}
