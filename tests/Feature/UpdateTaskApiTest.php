<?php

namespace Tests\Feature;

use App\Models\Level;
use App\Models\Project;
use App\Models\Quest;
use App\Models\Reward;
use App\Models\Sprint;
use App\Models\Task;
use App\Models\User;
use App\Models\UserQuest;
use App\Models\Workspace;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\base\BaseTestCase;
use Tests\TestCase;

class UpdateTaskApiTest extends BaseTestCase
{
    use DatabaseTransactions, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        $endDate = $this->faker->date;
        $beginDate = $this->faker->date('Y-m-d', $endDate);

        $this->user = User::factory()->create();
        $this->workspace = Workspace::factory()->create();
        $this->workspace->users()->attach($this->user->id);

        $this->project = Project::factory()->for($this->workspace)->create();

        $this->sprint = Sprint::factory([
            "begin_date" => $beginDate,
            "end_date" => $endDate
        ])->for($this->project)->create();

        $this->task = Task::factory()
            ->for($this->sprint)
            ->for($this->project)
            ->create();

        $this->unlink_workspace = Workspace::factory()->create();
        $this->unlink_project = Project::factory()->for($this->unlink_workspace)->create();
        $this->unlink_sprint = Sprint::factory()->for($this->unlink_project)->create();
        $this->unlink_task = Task::factory()
            ->for($this->unlink_project)
            ->for($this->unlink_sprint)
            ->create();

        $this->accessToken = $this->user->createToken('API Token')->accessToken;
        $this->header = ["Authorization" => "Bearer $this->accessToken", "Accept" => "application/json"];
    }

    private function generateUrl($workspaceId, $projectId, $taskId)
    {
        return "/api/workspaces/{$workspaceId}/projects/{$projectId}/tasks/{$taskId}";
    }

    /**
     * Allows to get generated data of body
     * @throws Exception
     */
    private function getGeneratedData() {
        $beginDate = new DateTime($this->sprint->begin_date);
        $endDate = new DateTime($this->sprint->end_date);

        return [
            'label' => $this->faker->text,
            'description' => $this->faker->text,
            'estimated_timestamp' => $this->faker->randomDigitNotNull,
            'realized_timestamp' => $this->faker->randomDigitNotNull,
            'deadline' => $this->faker->dateTimeBetween($beginDate, $endDate)->format('Y-m-d H:i:s'),
            'is_finish' => false,
            'priority_id' => random_int(1, 3),
            'status_id' => random_int(1, 3)
        ];
    }

    /**
     * Test successful update task
     * @return void
     * @throws Exception
     */
    public function testSuccessfulTaskUpdate()
    {
        $task = Task::factory()
            ->for($this->sprint)
            ->for($this->project)
            ->create();

        $newData = $this->getGeneratedData();

        $response = $this->putJson(
            $this->generateUrl($this->workspace->id, $this->project->id, $task->id),
            $newData,
            $this->header
        );

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Task updated successfully.'
            ]);

        $task->refresh();

        $this->assertEquals($newData['label'], $task->label);
        $this->assertEquals($newData['description'], $task->description);
        $this->assertEquals($newData['estimated_timestamp'], $task->estimated_timestamp);
        $this->assertEquals($newData['realized_timestamp'], $task->realized_timestamp);
        $this->assertEquals($newData['deadline'], $task->deadline);
        $this->assertEquals($newData['is_finish'], $task->is_finish);
        $this->assertEquals($newData['priority_id'], $task->priority_id);
        $this->assertEquals($newData['status_id'], $task->status_id);
    }

    public function testFinishTaskUpdateQuest() {
        $user = $this->user;

        $currentEarnedPointsOfUser = $user->earned_points;

        $task = Task::factory()
            ->for($this->sprint)
            ->for($this->project)
            ->create([
                'is_finish' => false
            ]);

        $questsWithFirstsLevelsOnlyQuery = UserQuest::query()
            ->join('quests', 'quests.id', '=', 'users_quests.quest_id')
            ->where('user_id', '=', $user->id)
            ->where('quests.level', '=', 1)
            ->where('quests.quest_types_id', '=', 2)
            ->orderBy('previous_quest_id')
            ->get()
            ->toArray();

        $newData = [
            'deadline' => $this->getDeadlineAfterToday(),
            'is_finish' => true
        ];

        $response = $this->putJson(
            $this->generateUrl($this->workspace->id, $this->project->id, $task->id),
            $newData,
            $this->header
        );

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Task updated successfully.'
            ]);

        $task->refresh();

        foreach($questsWithFirstsLevelsOnlyQuery as $quest) {
            $quest['completed_count'] += 1;
            if($quest['completed_count'] === $quest['count']) {
                $quest['is_completed'] = true;
                $quest['in_progress'] = false;

                $nextQuest = Quest::query()
                    ->where('previous_quest_id', '=', str($quest['id']))
                    ->first();

                $nextUserQuest = UserQuest::query()
                    ->join('quests', 'quests.id', '=', 'users_quests.quest_id')
                    ->where('user_id', '=', $user->id)
                    ->where('quests.level', '=', 2)
                    ->where('in_progress', '=', true)
                    ->where('quests.quest_types_id', '=', 2)
                    ->where('quest_id', '=', $nextQuest->id)
                    ->first();

                if(!$nextUserQuest) {
                    dd($quest);
                }

                $this->assertNotNull(
                    $nextUserQuest
                );

                $currentEarnedPointsOfUser += $quest['reward_points'];
            }
        }

        $this->assertEquals($newData['is_finish'], $task->is_finish);

        $user->refresh();

        $this->assertEquals($currentEarnedPointsOfUser, $user->earned_points);
    }

    public function testFinishTaskUpdateQuestButNotInTime() {
        $user = $this->user;

        $currentEarnedPointsOfUser = $user->earned_points;

        $task = Task::factory()
            ->for($this->sprint)
            ->for($this->project)
            ->create([
                'is_finish' => false
            ]);

        $questsWithFirstsLevelsOnlyQuery = UserQuest::query()
            ->join('quests', 'quests.id', '=', 'users_quests.quest_id')
            ->where('user_id', '=', $user->id)
            ->where('quests.level', '=', 1)
            ->where('quests.quest_types_id', '=', 2)
            ->where('quests.name', '=', 'Travail Dur')
            ->orderBy('previous_quest_id')
            ->get()
            ->toArray();

        $task->deadline = $this->getDeadlineBeforeToday();
        $task->save();

        $newData = [
            'is_finish' => true
        ];

        $response = $this->putJson(
            $this->generateUrl($this->workspace->id, $this->project->id, $task->id),
            $newData,
            $this->header
        );

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Task updated successfully.'
            ]);

        $task->refresh();

        foreach($questsWithFirstsLevelsOnlyQuery as $quest) {
            $quest['completed_count'] += 1;
            if($quest['completed_count'] === $quest['count']) {
                $quest['is_completed'] = true;
                $quest['in_progress'] = false;

                $nextQuest = Quest::query()
                    ->where('previous_quest_id', '=', $quest['id'])
                    ->first();

                $nextUserQuest = UserQuest::query()
                    ->join('quests', 'quests.id', '=', 'users_quests.quest_id')
                    ->where('user_id', '=', $user->id)
                    ->where('quests.level', '=', 2)
                    ->where('in_progress', '=', true)
                    ->where('quests.quest_types_id', '=', 2)
                    ->where('quest_id', '=', $nextQuest->id)
                    ->orderBy('previous_quest_id')
                    ->first();

                $this->assertNotNull(
                    $nextUserQuest
                );

                $currentEarnedPointsOfUser += $quest['reward_points'];
            }
        }

        $this->assertEquals($newData['is_finish'], $task->is_finish);

        $user->refresh();

        $this->assertEquals($currentEarnedPointsOfUser, $user->earned_points);
    }

    public function testFinishTaskUpdateQuestUpgradeLevel() {
        $user = $this->user;

        $currentLevel = Level::find($user->level_id);

        $nextLevelId = $currentLevel->id+1;
        $nextLevel = Level::find($currentLevel->id+1);

        $user->earned_points = $currentLevel->max_point - 5;
        $user->save();

        $currentEarnedPointsOfUser = $user->earned_points;

        $task = Task::factory()
            ->for($this->sprint)
            ->for($this->project)
            ->create([
                'is_finish' => false
            ]);

        $task->deadline = $this->getDeadlineBeforeToday();
        $task->save();

        $newData = [
            'is_finish' => true
        ];

        $response = $this->putJson(
            $this->generateUrl($this->workspace->id, $this->project->id, $task->id),
            $newData,
            $this->header
        );

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Task updated successfully.'
            ]);

        $user->refresh();

        $this->assertEquals($nextLevelId, $user->level_id);
        $this->assertLessThanOrEqual($nextLevel->max_point, $user->earned_points);
        $this->assertGreaterThanOrEqual($nextLevel->min_point, $user->earned_points);
    }

    public function testFinishTaskUpdateQuestUpgradeLevelAndReturnAssignedReward() {
        $user = $this->user;

        $currentLevel = Level::find($user->level_id);

        $nextLevelId = $currentLevel->id+1;

        $user->earned_points = $currentLevel->max_point - 5;
        $user->save();

        $next_reward = Reward::factory()->create(['level_id' => $nextLevelId]);

        $task = Task::factory()
            ->for($this->sprint)
            ->for($this->project)
            ->create([
                'is_finish' => false
            ]);

        $task->deadline = $this->getDeadlineBeforeToday();
        $task->save();

        $newData = [
            'is_finish' => true
        ];

        $response = $this->putJson(
            $this->generateUrl($this->workspace->id, $this->project->id, $task->id),
            $newData,
            $this->header
        );

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Task updated successfully.'
            ]);

        $user->refresh();
        $next_reward->refresh();

        $response->assertJsonIsObject('gain_reward');

        $this->assertEquals($user->id, $next_reward->user_id);
    }

    /**
     * Test task creation with unauthorized user
     *
     * @return void
     * @throws Exception
     */
    public function testTaskCreationWithUnauthorizedUser()
    {
        $response = $this->putJson(
            $this->generateUrl($this->workspace->id, $this->project->id, $this->task->id),
            $this->getGeneratedData()
        );

        $response->assertStatus(401); // Unauthorized
    }

    /**
     * Test update task with missing workspace
     *
     * @return void
     * @throws Exception
     */
    public function test_update_task_with_missing_workspace()
    {
        $this->workspace = Workspace::factory()->make();

        $response = $this->putJson(
            $this->generateUrl($this->workspace->id, $this->project->id, $this->task->id),
            $this->getGeneratedData(),
            $this->header
        );

        $response->assertStatus(404);
    }

    public function test_update_task_with_unlinked_project()
    {
        $response = $this->putJson(
            $this->generateUrl($this->workspace->id, $this->unlink_project->id, $this->task->id),
            $this->getGeneratedData(),
            $this->header
        );

        $response->assertStatus(403);
        $this->assertEquals($response->json("message"), "This project does not belong to the specified workspace.");
    }

    public function test_update_task_with_unlinked_sprint()
    {
        $data = $this->getGeneratedData();
        $data['sprint_id'] = $this->unlink_sprint->id;

        $response = $this->putJson(
            $this->generateUrl($this->workspace->id, $this->unlink_project->id, $this->task->id),
            $data,
            $this->header
        );

        $response->assertStatus(403);
        $this->assertEquals($response->json("message"), "This project does not belong to the specified workspace.");
    }
}
