<?php

namespace App\Presenters;

use Nette;
use App\Models\Employee;
use Nette\Application\UI\Form;

class EmployeePresenter extends Nette\Application\UI\Presenter
{
    private $employee;

    public function __construct(Employee $employee)
    {
        parent::__construct();
        $this->employee = $employee;
    }

    public function renderIndex()
    {
        $this->template->employees = $this->employee->list();
    }
    
    public function renderGraph()
    {    
        $xmlString = file_get_contents('files/xml/employees.xml');
        $xml = new \SimpleXMLElement($xmlString);
    
        $ageCounts = array_fill_keys(range(18, 65), 0);
    
        foreach ($xml->Employee as $employee) {
            $age = (int)$employee->Age;
    
            if (array_key_exists($age, $ageCounts)) {
                $ageCounts[$age]++;
            }
        }
    
        $ageCounts = array_filter($ageCounts, function($count) { return $count > 0; });
    
        $dataForChart = json_encode($ageCounts);
    
        $this->template->jsonData = $dataForChart;
    }

    protected function createComponentEmployeeForm(): Form
    {
        $form = new Form;

        $form->addText('fname', 'First Name: *')
            ->setRequired('Please enter the employee\'s first name.');

        $form->addText('lname', 'Last Name: *')
            ->setRequired('Please enter the employee\'s last name.');

        $form->addSelect('gender', 'Gender: *', ['Male' => 'Male', 'Female' => 'Female'])
            ->setDefaultValue('Male')
            ->setRequired('Please select the employee\'s gender.');

        $form->addText('age', 'Age: *')
            ->setRequired('Please enter the employee\'s age.')
            ->addRule($form::INTEGER, 'Age must be an integer.')
            ->setHtmlAttribute('type', 'number')
            ->setHtmlAttribute('step', '1')
            ->setHtmlAttribute('min', '18') 
            ->setHtmlAttribute('max', '65'); 

        $form->addText('identification_number', 'Identification number: *')
            ->setRequired('Please enter the employee\'s identification number.')
            ->addRule($form::PATTERN, 'Identification number must have 10 characters.', '\d{6}/\d{4}')
            ->addRule(function ($control) {
                $id = $this['employeeForm']['id']->getValue(); // Získanie ID z hidden inputu, ak je nastavené
                $exists = $this->employee->existsByIdentificationNumber($control->value, $id);
                return !$exists;
            }, 'Employee with this Identification Number already exists.')
            ->setHtmlAttribute('pattern', '\d{6}/\d{4}')
            ->setHtmlAttribute('title', 'Please enter a valid identification number. It should have 6 digits followed by a slash (/) and then 4 more digits.')
            ->setHtmlAttribute('maxlength', '11');
        
        $form->addText('month_salary', 'Monthly Salary:')
            ->addRule($form::FLOAT, 'Monthly salary must be a number.')
            ->setHtmlAttribute('type', 'number')
            ->setHtmlAttribute('step', '0.01');

        $form->addText('hired_at', 'Hired At:')
            ->setHtmlAttribute('type', 'date');

        $form->addSubmit('send', 'Save Employee');

        $form->addHidden('id'); 

        $form->onSuccess[] = function (Form $form, \stdClass $values): void {
            if (!empty($values->id)) {
                $this->employee->edit($values->id, (array)$values);
                $this->flashMessage('Employee updated successfully.', 'success');
            } else {
                $this->employee->create((array)$values);
                $this->flashMessage('Employee created successfully.', 'success');
            }
            $this->redirect('Employee:index');
        };

        return $form;
    }


    public function actionEdit($id)
    {
        $employee = $this->employee->getById($id);
        if (!$employee) {
            $this->error('Employee not found');
        }

        $employeeData = (array) $employee;
        $employeeData['id'] = $id; 
        $this['employeeForm']->setDefaults($employeeData);
    }

    public function handleDelete($id)
    {
        $this->employee->delete($id);
        $this->flashMessage('Employee deleted successfully.', 'success');
        $this->redirect('Employee:index');
    }
}