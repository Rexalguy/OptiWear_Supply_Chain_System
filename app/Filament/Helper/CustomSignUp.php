<?php

namespace App\Filament\Helper;

use Filament\Pages\Auth\Register;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Component;



class CustomSignup extends Register 
{
        protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        $this->getNameFormComponent(),
                        $this->getEmailFormComponent(),
                        $this->getDateOfBirthFormComponent(),
                        $this->getGenderFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                    ])
                    ->statePath('data'),
            ),
        ];
    }


            protected function getDateOfBirthFormComponent(): Component
        {
            return DatePicker::make('date_of_birth')
                ->label('Date of Birth')
                ->required()
                ->maxDate(now());
        }

        protected function getGenderFormComponent(): Component
        {
            return Select::make('gender')
                ->label('Gender')
                ->required()
                ->options([
                    'male' => 'Male',
                    'female' => 'Female',
                    'other' => 'Other',
                ])
                ->native(false); // Use dropdown UI
        }
}