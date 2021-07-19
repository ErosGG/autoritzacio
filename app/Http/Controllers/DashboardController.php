<?php

namespace App\Http\Controllers;

use App\Models\Link;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class DashboardController extends Controller
{
    public function index()
    {
        //$links = Link::all();
        $user = Auth::user();
        //$links = User::find($user->id)->links()->get();
        $links = $user->links()->get();
        return view('dashboard')
            ->with([
                "userName" => $user->name,
                "links" => $links,
            ]);
    }


    public function create(Request $request)
    {
        $user = Auth::user();
        $data = $request->validate([
            "title" => "required",
            "url" => ["required", "url"],
        ], [
            "title.required" => "El camp títol és obligatori",
            "url.required" => "El camp URL és obligatori",
            "url.url" => "El camp URL ha de ser una URL vàlida",
        ]);
        $data["url"] = strtolower($data["url"]);
        if (DB::table("links")
            ->where("url", $request->url)
            ->get()->isEmpty()) {
                $link = Link::create([
                    "title" => $data["title"],
                    "url" => $data["url"],
                ]);
                DB::table("link_user")->insert([
                    "link_id" => $link->id,
                    "user_id" => $user->id,
                ]);
        } else {
            $link = Link::firstWhere("url", $request->url);
            if (DB::table("link_user")
                ->where("user_id", $user->id)
                ->where("link_id", $link->id)
                ->get()->isEmpty()) {
                    DB::table("link_user")->insert([
                        "link_id" => $link->id,
                        "user_id" => $user->id,
                    ]);
            } else {
                $request->validate([
                    "url" => ["required", "url", "unique:links,url"],
                ], [
                    "url.unique" => "La URL no pot ser repetida",
                ]);
            }
        }
        return view("dashboard")
            ->with([
                "userName" => $user->name,
                "links" => $user->links()->get(),
            ]);
    }


    public function details(Link $link)
    {
        return view("link-details")
            ->with("link", $link);
    }


    public function edit(Link $link)
    {
        if (Gate::denies("edit-link", $link)) {
            $user = Auth::user();
            return redirect()
                ->route("links.index")
                ->with([
                    "userName" => $user->name,
                    "links" => $user->links()->get(),
                ]);
        }
        return view("edit-link")->with("link", $link);
    }


    public function update(Link $link, Request $request): RedirectResponse
    {
        $user = Auth::user();
        $data = $request->validate([
            "title" => "required",
            "url" => ["required", "url"],
        ], [
            "title.required" => "El camp títol és obligatori",
            "url.required" => "El camp URL és obligatori",
            "url.url" => "El camp URL ha de ser una URL vàlida",
        ]);
        $data["url"] = strtolower($data["url"]);
        $multipleOwners = DB::table("link_user")
            ->where("link_id", $link->id)
            ->count() > 1;
        $alreadyExists = Link::where("url", $data["url"])
            ->where("title", $data["title"])
            ->exists();
        if ($alreadyExists) {
            $existingLink = Link::where("url", $data["url"])
                ->where("title", $data["title"])
                ->first();
        }
        if ($multipleOwners && $alreadyExists) {
            DB::table("link_user")
                ->where("link_id", $link->id)
                ->where("user_id", $user->id)
                ->update(["link_id" => $existingLink->id]);
            $link = $existingLink;
        } elseif ($multipleOwners && ! $alreadyExists) {
            $newLink = Link::create([
                "title" => $data["title"],
                "url" => $data["url"],
            ]);
            DB::table("link_user")
                ->where("link_id", $link->id)
                ->where("user_id", $user->id)
                ->update(["link_id" => $newLink->id]);
            $link = $newLink;
        } elseif (! $multipleOwners && $alreadyExists) {
            if ($link != $existingLink) {
                DB::table("link_user")
                    ->where("link_id", $link->id)
                    ->where("user_id", $user->id)
                    ->update(["link_id" => $existingLink->id]);
                $link->forceDelete();
                $link = $existingLink;
            }
        } else {
            $link->update($data);
        }
        return redirect()->route("link.details", ["link" => $link]);
    }


    public function delete(Link $link): RedirectResponse
    {
        /*
         if (Auth::user()->can("delete", $link)) {
             dd("Controlador: SÍ pot eliminar el post");
             $link->delete();
         } else {
             dd("Controlador: NO pot eliminar el post");
         }
        */
        $user = Auth::user();
        DB::table("link_user")
            ->where("link_id", $link->id)
            ->where("user_id", $user->id)
            ->delete();
        if (DB::table("link_user")
                ->where("link_id", $link->id)
                ->get()->isEmpty()) {
            $link->forceDelete();
        }
        return redirect()->route("links.index", ["links" => $user->links()->get()]);
    }

}
