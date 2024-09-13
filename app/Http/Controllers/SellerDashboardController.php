<?php

// app/Http/Controllers/SellerDashboardController.php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\AuctionItem;
use App\Models\Category;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SellerDashboardController extends Controller
{
    public function index()
    {
        $seller = Auth::user(); // Assuming the authenticated user is the seller

        // Get the count of auctioned items for this seller
        $auctionedItemsCount = $seller->auctionItems()->count();

        // Assuming 'status' or 'is_sold' indicates whether an item is sold
        $soldItemsCount = $seller->auctionItems()->where('status', 'sold')->count();

        return view('seller.dashboard', compact('seller', 'auctionedItemsCount', 'soldItemsCount'));
    }

    public function createAuctionItem()
    {
        $categories = Category::all(); // Fetch all available categories
        return view('seller.add-item', compact('categories'));
    }

    public function storeAuctionItem(Request $request)
    {
        // Validate the form inputs
        $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'starting_price' => 'required|numeric',
            'current_bid' => 'nullable|numeric',
            'end_time' => 'required|date',
            'categories' => 'required|array',
            'image_paths.*' => 'nullable|image|max:5120', // Ensure image paths are valid strings
        ]);

        // Create the auction item
        $auctionItem = AuctionItem::create([
            'title' => $request->title,
            'description' => $request->description,
            'starting_price' => $request->starting_price,
            'current_bid' => $request->current_bid,
            'end_time' => $request->end_time,
            'seller_id' => auth()->id(), // Assign the current logged-in seller's ID
        ]);

        // Attach the categories to the auction item
        $auctionItem->categories()->attach($request->categories);

        // Handling image paths
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                if ($image) {
                    // Store the image in the 'auction_images' folder in the 'public' disk
                    $imagePath = $image->store('auction_images', 'public');

                    // Save the image path to the database
                    Image::create([
                        'url' => '/storage/' . $imagePath,  // Store the URL to the image
                        'auction_item_id' => $auctionItem->id,
                    ]);
                }
            }
        }


        // Redirect the seller back to the dashboard with a success message
        return redirect()->route('seller.dashboard')->with('success', 'Auction item added successfully!');
    }
}
