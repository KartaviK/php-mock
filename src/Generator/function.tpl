namespace {namespace};

use Kartavik\PHPMock\Generator\MockFunction;

function {name}({signatureParameters})
{
    $arguments = [{bodyParameters}];

    $variadics = \array_slice(\func_get_args(), \count($arguments));
    $arguments = \array_merge($arguments, $variadics);

    return MockFunction::call(
        '{name}',
        '{fqfn}',
        $arguments
    );
}