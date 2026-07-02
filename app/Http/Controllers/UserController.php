<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CvTemplate;

class UserController extends Controller
{
    public function dashboard()
    {
        $templates = CvTemplate::where('is_active', true)->orderBy('sort_order')->get();
        return view('user.dashboard', compact('templates'));
    }

    public function manuscript(Request $request)
    {
        if ($request->has('cv_id')) {
            $cv = auth()->user()->cvs()->findOrFail($request->cv_id);
        } else {
            $templates = CvTemplate::where('is_active', true)->orderBy('sort_order')->get();
            return view('user.manuscripts_index', compact('templates'));
        }

        $required = ['personal_info', 'work_experience', 'education', 'skills', 'target_job'];
        $existing = $cv->sections()->pluck('type')->toArray();
        $missing  = array_diff($required, $existing);
        
        if (!empty($missing)) {
            foreach ($missing as $type) {
                $cv->sections()->create([
                    'type' => $type,
                    'title' => ucwords(str_replace('_', ' ', $type)),
                    'order' => array_search($type, $required) + 1,
                    'content' => null
                ]);
            }
            $cv->load('sections');
        }
        
        $templates = CvTemplate::where('is_active', true)->orderBy('sort_order')->get();
        return view('user.manuscript', compact('templates', 'cv'));
    }

    public function aiAssistant()
    {
        $user = auth()->user();
        $cvs = $user->cvs()->with('sections')->latest()->get();
        
        $quota = [
            'remaining'  => $user->getQuotaRemaining(),
            'limit'      => $user->getQuotaLimit(),
            'percentage' => $user->getQuotaPercentage(),
        ];
        
        return view('user.ai-assistant', compact('cvs', 'quota'));
    }

    public function settings()
    {
        return view('user.settings');
    }

    public function upgradeQuota()
    {
        $subscription = auth()->user()
            ->subscriptions()
            ->where('plan', 'premium')
            ->whereIn('status', ['active', 'cancelled'])
            ->where(function ($query) {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>', now());
            })
            ->latest('created_at')
            ->first()
            ?? auth()->user()
                ->subscriptions()
                ->where('plan', 'premium')
                ->latest('created_at')
                ->first();

        return view('user.upgrade-quota', compact('subscription'));
    }
}
