<?php

namespace App\Console\Commands;

use App\Neuron\DevAssistAgent;
use Illuminate\Console\Command;
use Inspector\Laravel\Facades\Inspector;
use NeuronAI\Chat\Messages\UserMessage;
use NeuronAI\Observability\AgentMonitoring;

class Agent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:agent';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $response = DevAssistAgent::make()
        ->observe(new AgentMonitoring(Inspector()))
        ->chat(
            new UserMessage('Hi, who are you')
        );

        echo $response->getContent();
    }
}
