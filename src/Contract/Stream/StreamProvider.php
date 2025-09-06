<?php
declare(strict_types=1);


namespace Philiagus\Figment\Cli\Contract\Stream;

interface StreamProvider {

    public function in(int $number): InStream;

    public function out(int $number): OutStream;

    public function stdin(): InStream;

    public function stdout(): OutStream;
    public function stderr(): OutStream;

}
