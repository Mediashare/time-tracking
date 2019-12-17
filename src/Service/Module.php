<?php
namespace Mediashare\Service;
class Module
{
    public $modules_dir = __DIR__.'/../Modules/*.sh';
    public $modules = [];
    public $variables = [];
    public $command;

    public function __construct(?array $variables) {
        $this->variables = $this->getVariables($variables);
        $this->modules = $this->getModules();
    }

    public function execute(?string $module_name) {
        $module = $this->getModule($module_name);
        $module['result'] = \shell_exec($module['content']);
        return $module;
    }

    public function getVariables(?array $variables) {
        foreach ($variables as $key => $variable) {
            $this->variables[] = json_decode($variable, true);
        }
        return $this->variables;
    }

    public function getModule(string $module) {
        if (!empty($this->modules[$module])):
            return $this->modules[$module];
        elseif (!empty($this->modules[$module . '.sh'])):
            return $this->modules[$module . '.sh'];
        else:
            return false;
        endif;
    }

    public function getModules() {
        $modules = glob($this->modules_dir);
        foreach ($modules as $module) {
            $filename = basename($module);
            $command = [
                'fileDir' => $module,
                'filename' => $filename,
                'name' => rtrim($filename, '.sh'),
                'content' => file_get_contents($module)
            ];

            // Check if variables exist
            // dump($this->variables);die;
            foreach ($this->variables as $variables_injected) {
                if (isset($variables_injected[$command['name']])):
                    foreach ($variables_injected[$command['name']] as $variable => $value):
                        if (\strpos($command['content'], '%'.$variable.'%') !== false):
                            $command['content'] = str_replace('%'.$variable.'%', $value, $command['content']);
                        endif;
                    endforeach;
                endif;
            }

            // Record
            $commands[rtrim(basename($module), '.sh')] = $command;
        }
        return $commands;
    }
}
