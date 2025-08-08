<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Task;

class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_task()
    {
        $data = [
            'title' => 'Test Task',
            'description' => 'Test description',
            'status' => 'pending',
        ];
        $response = $this->postJson('/api/tasks', $data);
        $response->assertStatus(201)
            ->assertJsonFragment(['title' => 'Test Task']);
        $this->assertDatabaseHas('tasks', ['title' => 'Test Task']);
    }

    public function test_cannot_create_task_without_title()
    {
        $data = [
            'description' => 'No title',
            'status' => 'pending',
        ];
        $response = $this->postJson('/api/tasks', $data);
        $response->assertStatus(422);
    }

    public function test_can_list_tasks()
    {
        Task::factory()->create(['title' => 'Task 1']);
        Task::factory()->create(['title' => 'Task 2']);
        $response = $this->getJson('/api/tasks');
        $response->assertStatus(200)
            ->assertJsonFragment(['title' => 'Task 1'])
            ->assertJsonFragment(['title' => 'Task 2']);
    }

    public function test_can_show_task()
    {
        $task = Task::factory()->create(['title' => 'Show Task']);
        $response = $this->getJson('/api/tasks/' . $task->id);
        $response->assertStatus(200)
            ->assertJsonFragment(['title' => 'Show Task']);
    }

    public function test_can_update_task()
    {
        $task = Task::factory()->create(['title' => 'Old Title']);
        $data = ['title' => 'New Title', 'description' => 'Updated', 'status' => 'done'];
        $response = $this->putJson('/api/tasks/' . $task->id, $data);
        $response->assertStatus(200)
            ->assertJsonFragment(['title' => 'New Title']);
        $this->assertDatabaseHas('tasks', ['title' => 'New Title']);
    }

    public function test_can_delete_task()
    {
        $task = Task::factory()->create();
        $response = $this->deleteJson('/api/tasks/' . $task->id);
        $response->assertStatus(200)
            ->assertJsonFragment(['message' => 'Task deleted']);
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }
}

