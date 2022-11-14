<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Http\Request;
use App\Models\SubClaim;

class SubClaims extends Component
{


    public $subclaims, $text, $claim_id;
    public $updateMode = false;
    public $inputs = [];
    public $i = 1;

    public function add($i)
    {
        $i = $i + 1;
        $this->i = $i;
        array_push($this->inputs ,$i);
    }

    public function remove($i)
    {
        unset($this->inputs[$i]);
    }

    private function resetInputFields(){
        $this->text = '';
        $this->claim_id = '';
    }


    public function store()
    {
        $validatedDate = $this->validate([
                'claim_id.0' => 'required',
                'text.0' => 'required',
                'claim_id.*' => 'required',
                'text.*' => 'required',
            ],
            [
                'claim_id.0.required' => 'claim_id field is required',
                'text.0.required' => 'text field is required',
                'claim_id.*.required' => 'claim_id field is required',
                'text.*.required' => 'text field is required',
            ]
        );
   
        foreach ($this->text as $key => $value) {
            SubClaim::create(['claim_id' => $this->claim_id[$key], 'text' => $this->text[$key]]);
        }
  
        $this->inputs = [];
   
        $this->resetInputFields();
   
        session()->flash('message', 'Sous-réclamation ajoutée avec succès.');
    }

    public function render()
    {
        $data = SubClaim::all();
        return view('livewire.sub-claims',['data' => $data]);
    }
}
