<?php


namespace Tests\Unit\Http\Requests;

use Bluewing\Http\Requests\BluewingFormRequest;
use Closure;
use Illuminate\Validation\ValidationException;
use Orchestra\Testbench\TestCase;
use ReflectionClass;
use ReflectionException;

class MocksBluewingFormRequest extends BluewingFormRequest {

    /**
     * @var string[][] -
     */
    public array $rules = [];

    /**
     * @return string[][] -
     */
    public function rules()
    {
        return $this->rules;
    }
}

final class BluewingFormRequestTest extends TestCase
{
    /**
     * @var string[][]
     */
    protected array $rules = ['validProperty' => ['required']];

    /**
     *
     * @throws ReflectionException -
     */
    public function test_allow_list_denies_all_properties_if_empty()
    {
        $requestData = ['validProperty' => 'testing', 'invalidProperty' => 'testing'];
        $allowlist   = [];

        $this->mockRequestClass($this->rules, $requestData, $allowlist, function() {
            $this->expectException(ValidationException::class);
        });
    }

    /**
     *
     * @throws ReflectionException -
     */
    public function test_allow_list_allows_property_if_provided()
    {
        $requestData = ['validProperty' => 'testing', 'propertyWithNoRules' => 'testing'];
        $allowlist   = ['propertyWithNoRules'];

        $this->mockRequestClass($this->rules, $requestData, $allowlist, function() {
            $this->expectNotToPerformAssertions();
        });
    }

    /**
     * @throws ReflectionException -
     */
    public function test_ignores_properties_specified_in_rules()
    {
        $requestData = ['validProperty' => 'testing'];
        $allowlist   = [];

        $this->mockRequestClass($this->rules, $requestData, $allowlist, function() {
            $this->expectNotToPerformAssertions();
        });
    }

    /**
     * @param string[][] $rules -
     * @param string[] $requestData -
     * @param string[] $allowlist -
     * @param callable|null $beforeHook -
     *
     * @throws ReflectionException
     */
    private function mockRequestClass(array $rules, array $requestData, array $allowlist, callable $beforeHook = null)
    {
        $request = MocksBluewingFormRequest::create('/fake', 'POST', $requestData);
        $request->rules = $rules;
        $request->setContainer(app());

        $r = new ReflectionClass($request);
        $p = $r->getProperty('allowlist');
        $p->setAccessible(true);
        $p->setValue($request, $allowlist);

        $m = $r->getMethod('getValidatorInstance');
        $m->setAccessible(true);

        if (!is_null($beforeHook)) {
            Closure::fromCallable($beforeHook)->call($this);
        }

        $m->invoke($request);
    }
}