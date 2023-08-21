<?php

namespace App\Http\Controllers;

use App\Services\RewardService;
use Illuminate\Http\Request;

class RewardApiController extends Controller
{
    private RewardService $rewardService;

    public function __construct()
    {
        $this->rewardService = new RewardService();
    }

    public function index(Request $request) {
        $user = $request->user();

        $rewards = $this->rewardService->getAllRewardsOfUser($user);

        return $rewards;
    }
}
