<?php


namespace Okay\Core\Console;


use Okay\Core\ServiceLocator;
use \Symfony\Component\Console\Command\Command AS SymfonyCommand;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

class Command extends SymfonyCommand
{
    /** @var InputInterface */
    protected $input;

    /** @var OutputInterface */
    protected $output;

    /** @var ServiceLocator */
    protected $serviceLocator;

    /** @var QuestionHelper */
    protected $questionHelper;

    public function __construct($name = null)
    {
        parent::__construct($name);

        $this->serviceLocator = ServiceLocator::getInstance();
        $this->questionHelper = $this->serviceLocator->getService(QuestionHelper::class);
    }

    final protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->input = $input;
        $this->output = $output;

        if (!method_exists($this, 'handle')) {
            throw new \Exception("Command must implement method 'handle'.");
        }
        return call_user_func_array([$this, 'handle'], $this->getHandleArguments());
    }

    private function getHandleArguments(): array
    {
        $reflectionMethod = new \ReflectionMethod($this, 'handle');

        return array_reduce($reflectionMethod->getParameters(), function($arguments, $parameter) {
            if (($type = $parameter->getType()) !== null) {
                $typeName = $type->getName();
                if ($this->serviceLocator->hasService($typeName)) {
                    $arguments[] = $this->serviceLocator->getService($typeName);
                } else {
                    $arguments[] = new $typeName();
                }
            } else {
                $arguments[] = null;
            }

            return $arguments;
        }, []);
    }

    protected function ask(string $question, $default = null)
    {
        return $this->questionHelper->ask(
            $this->input,
            $this->output,
            new Question($question, $default)
        );
    }

    protected function askConfirmation(string $question, bool $default = true, string $trueAnswerRegex = '/^y/i')
    {
        return $this->questionHelper->ask(
            $this->input,
            $this->output,
            new ConfirmationQuestion($question, $default, $trueAnswerRegex)
        );
    }

    protected function askChoice(string $question, array $choices, $default = null)
    {
        return $this->questionHelper->ask(
            $this->input,
            $this->output,
            new ChoiceQuestion($question, $choices, $default)
        );
    }
}