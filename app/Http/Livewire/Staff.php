<?php

namespace App\Http\Livewire;

use Livewire\Component;



class Staff extends Component
{
    public $userroles, $first_name, $last_name, $email;
    public $updateMode = false;

    public function render()
    {
        $this->userroles = userroles::all();
        return view('livewire.staff');
    }

    public function store()
    {
        $validatedDate = $this->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required',
            'dob' => 'required',
            'mobile_number' => 'required',
        ]);

        userroles::create($validatedDate);

        session()->flash('message', 'Staff Created Successfully.');

        $this->resetInputFields();

    }
}
