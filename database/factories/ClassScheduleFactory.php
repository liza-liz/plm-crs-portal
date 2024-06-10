<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ClassScheduleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $classes = \App\Models\Classes::all()->random();
        
        $mode = \App\Models\ClassMode::all()->random();
        $room = \App\Models\Room::all()->random();
        $day = $this->faker->randomElement(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']);
        $start = date("g:i A", strtotime($this->faker->time()));
        $end = date("g:i A", strtotime($this->faker->time()));

        if ($day == 'Thursday'){
            $letter = substr($day, 0, 2);
        }else {
            $letter = $day[0];
        }

        return [
            'classes_id' => $classes->class_id,
            'day' => $day,
            'start_time' => $start,
            'end_time'=> $end,
            'mode_id' => $mode->mode_id,
            'room_id' => $room->room_id,
            'schedule_name' => $letter . ' ' . $start . ' - ' . $end . ' ' . $mode->mode_code . ' ' . $room->room_name,
        ];
    }
}
