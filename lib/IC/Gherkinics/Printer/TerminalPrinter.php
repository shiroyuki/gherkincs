<?php
/**
 * @copyright 2013 Instaclick Inc.
 */
namespace IC\Gherkinics\Printer;

use IC\Gherkinics\Util\Output;

/**
 * Terminal Printer
 *
 * This printer uses the standard output.
 *
 * @author Juti Noppornpitak <jnopporn@shiroyuki.com>
 */
class TerminalPrinter
{
    /**
     * @var \IC\Gherkinics\Util\Output
     */
    private $output;

    /**
     * @var string
     */
    private $basePath;

    /**
     * Constructor
     *
     * @param \IC\Gherkinics\Util\Output $output
     * @param string                     $basePath
     */
    public function __construct(Output $output, $basePath)
    {
        $this->output   = $output;
        $this->basePath = $basePath;
    }

    /**
     * Display feedback
     *
     * @param array $pathToFeedbackMap
     */
    public function doPrint(array $pathToFeedbackMap)
    {
        $pathOffset = strlen($this->basePath) + 1;

        foreach ($pathToFeedbackMap as $path => $lineToFeedbackListMap) {
            if ( ! $lineToFeedbackListMap) {
                continue;
            }

            $previousFeedback            = null;
            $lineNumberListWithSameError = array();

            if ( ! $lineToFeedbackListMap->all()) {
                continue;
            }

            $this->output->writeln(substr($path, $pathOffset));

            foreach ($lineToFeedbackListMap->all() as $lineNo => $feedbackList) {
                if ($previousFeedback == $feedbackList->all()) {
                    $lineNumberListWithSameError[] = $lineNo;

                    continue;
                }

                if ($lineNumberListWithSameError) {
                    $this->output->writeln('');
                    $this->displayLineNumbersHavingThePreviousErrors($lineNumberListWithSameError);
                    $this->output->writeln('');
                }

                $this->output->writeln('  line ' . $lineNo . ':');
                $this->output->writeln('    - ' . implode('.' . PHP_EOL . '    - ', $feedbackList->all()) . '.');

                $previousFeedback            = $feedbackList->all();
                $lineNumberListWithSameError = array();
            }

            if ($lineNumberListWithSameError) {
                $this->output->writeln('');
                $this->displayLineNumbersHavingThePreviousErrors($lineNumberListWithSameError);
            }

            $this->output->writeln('');
        }
    }

    /**
     * Display line numbers which have the previous errors
     *
     * @param array $lineNumberList
     */
    private function displayLineNumbersHavingThePreviousErrors(array $lineNumberList)
    {
        $this->output->writeln(
            '  ... the previous set of errors also occurs on lines: '
            . implode(', ', $lineNumberList)
        );
    }
}
