<?php

namespace Database\Factories;

use App\Models\probationer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProbationerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = probationer::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $gender = $this->faker->randomElement(['male', 'female']);

        return [
            'batch_id'   => $this->faker->numberBetween(1,3),
            'RollNumber'   => $this->faker->numberBetween(1,200),
            'Cadre'   => $this->faker->numerify('C-###'),
            'Name'   => $this->faker->name($gender),
            'gender'   => $gender,
            'Dob'   => "1995-01-01",
            'Religion'   => $this->faker->numberBetween(1,5),
            'Category'   => $this->faker->numberBetween(1,5),
            'MartialStatus'   => $this->faker->randomElement(['Married', 'Unmarried']),
            'MotherName'   =>  $this->faker->name("female"),
            'Moccupation'   => $this->faker->jobTitle,
            'FatherName'   => $this->faker->name("male"),
            'Foccupation'   => $this->faker->jobTitle,
            'Stateofdomicile'   => "TS",
            'Hometown'   => "Hyderabad",
            'District'   => "Hyderabad",
            'HomeAddress'   => $this->faker->address,
            'State'   => "TS",
            'Pincode'   => $this->faker->randomNumber(6),
            'phoneNumberStd'   => '9' . $this->faker->randomNumber(9),
            'MobileContactNumber'   => '9' . $this->faker->randomNumber(9),
            'OtherState'   => "TS",
            'EmergencyName'   => '9' . $this->faker->randomNumber(9),
            'EmergencyPhone'   => '9' . $this->faker->randomNumber(9),
            'EmergencyEmailId'   => $this->faker->safeEmail,
            'EmergencyAddress'   => $this->faker->address,
            // 'name' => $this->faker->name,
            // 'email' => $this->faker->unique()->safeEmail,
            // 'email_verified_at' => now(),
            // 'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            // 'remember_token' => Str::random(10),
        ];
    }
}
