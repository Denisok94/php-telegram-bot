<?php

namespace denisok94\telegram\components;

use denisok94\telegram\Bot;
use denisok94\telegram\request\Message;

/**
 * ```php
 * $command = new Command($bot);
 * $command->addStart(function (Message $message, Bot $bot) {
 *  return $bot->sendMessage([
 *      'chat_id' => $message->chat->id,
 *      'text' => 'Здравствуйте, товарищ!',
 *  ]);
 * });
 * $command->parse();
 * ```
 */
class Command
{
    private Bot $bot;
    private ?Message $message = null;
    private array $commands = [];
    public function __construct(Bot $bot)
    {
        $this->bot = $bot;
        if ($this->bot->getMessage()) {
            $this->setMessage($this->bot->getMessage());
        }
    }

    // ---- 

    /**
     * Зарегистрировать команду
     * ```php
     * $command->add('/start', function (Message $message, Bot $bot) {
     *  $text = $bot->isAdmin() ? 'Привет, создатель!' : 'Здравствуйте, товарищ!';
     *  return $bot->sendMessage($text);
     * });
     * ```
     * @param string $name
     * @param callable $callback
     * @return self
     */
    public function add(string $name, callable $callback): self
    {
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException('Неверный callback');
        }
        $this->commands[$name] = $callback;
        return $this;
    }

    /**
     * @param string $name
     * @param object $object
     * @param string $method
     * @return self
     * 
     * ```php
     * class MathOperations {
     *  public function multiply(Message $message, Bot $bot) {
     *      return $bot->sendMessage('Привет');
     *  }
     * }
     * $math = new MathOperations();
     * $command->addCommandMethodCallback('/start', $math, 'multiply');
     * ```
     */
    public function addCommandMethodCallback(string $name, object $object, string $method): self
    {
        $this->commands[$name] = [$object, $method];
        return $this;
    }

    public function getCommand(string $name): ?callable
    {
        return $this->commands[$name] ?? null;
    }
    public function getCommands(): array
    {
        return $this->commands;
    }
    public function setCommands(array $commands): self
    {
        $this->commands = $commands;
        return $this;
    }
    // ---- 
    public function addStart(callable $callback): self
    {
        return $this->add('/start', $callback);
    }
    public function addHelp(callable $callback): self
    {
        return $this->add('/help', $callback);
    }
    public function addAbout(callable $callback): self
    {
        return $this->add('/about', $callback);
    }

    // ---- 

    public function getMessage(): ?Message
    {
        return $this->message;
    }

    /**
     * Если нужно обработать другое сообщение
     * @param Message $message
     * @return Command
     */
    public function setMessage(Message $message): self
    {
        $this->message = $message;
        return $this;
    }

    // ---- 

    /**
     * @return:
     * - `false` - if message not bot_command
     * - `null` - command not found
     * - `mixed` - return result callback
     */
    public function parse()
    {
        if ($this->getMessage()?->type === 'bot_command') {
            if ($callback = $this->getCommand($this->getMessage()->text)) {
                // return ($callback)($this->message, $this->bot);
                return call_user_func($callback, $this->getMessage(), $this->bot);
            } else {
                return null;
            }
        } else {
            return false;
        }
    }
}
