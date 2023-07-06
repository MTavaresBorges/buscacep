<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Models\Address;
use Livewire\Component;
use WireUi\Traits\Actions;
use Livewire\WithPagination;
use App\Actions\AddressStoreAction;
use App\Services\ViaCep\ViaCepService;
use App\Actions\AddressGetPropertiesAction;
use App\Http\Livewire\Traits\AddressPropertiesRulesValidationTrait;
use App\Http\Livewire\Traits\AddressPropertiesMessagesValidationTrait;

class SearchZipcode extends Component
{
    use Actions;
    use AddressPropertiesRulesValidationTrait;
    use AddressPropertiesMessagesValidationTrait;
    use WithPagination;

    public array $data = [
        
    ];

    public string $search = '';

    protected $queryString = ['search'];

    public function updated(string $key, string $value): void{

        if($key === 'data.zipcode'){
            $this->data = ViaCepService::handle($value);
        }
    }

    public function save(): void
    {
        sleep(2);

        $this->validate();
        AddressStoreAction::save($this->data);

        //Atualizar pagina
        $this->showNotification('Criação/Atualização', 'O endereço foi criando/atualizado com sucesso');
        $this->resetExcept('data');
    }
    public function edit(string $id): void{

        $this->data = AddressGetPropertiesAction::handle($id); 
    }

    public function remove(string $id): void{

        $address = Address::find($id)?->delete();
        $this->showNotification('Excluisão de Endereço', 'Endereço excluído com sucesso!');
    }

    private function showNotification(string $title, string $message): void{
        $this->notification()->success($title, $message);
    }

    public function getAddressProperty(){
        if ($this->search) {
            return Address::where('street', 'like', "%{$this->search}%")->paginate(2);
        }
        return Address::paginate(2);
    }

    public function mount(): void{
        $this->data = AddressGetPropertiesAction::getEmptyProperties();
    }

    public function render(){
        return view('livewire.search-zipcode');
    }
}