<?php

use App\Models\User;

it('renders the budget, recurring, and savings goal pages for authenticated users', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $this->get(route('budgets.index'))->assertStatus(200);
    $this->get(route('recurrings.index'))->assertStatus(200);
    $this->get(route('savings-goals.index'))->assertStatus(200);
});
