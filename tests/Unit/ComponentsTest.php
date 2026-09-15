<?php

use Ugarit\Ranger\Components\BroadcastChannel;
use Ugarit\Ranger\Components\BroadcastEvent;
use Ugarit\Ranger\Components\Enum;
use Ugarit\Ranger\Components\EnvironmentVariable;
use Ugarit\Ranger\Components\InertiaResponse;
use Ugarit\Ranger\Components\InertiaSharedData;
use Ugarit\Ranger\Components\JsonApiResponse;
use Ugarit\Ranger\Components\JsonResponse;
use Ugarit\Ranger\Components\Model;
use Ugarit\Ranger\Components\ResourceResponse;
use Ugarit\Ranger\Components\Validator;
use Ugarit\Ranger\Support\Verb;
use Ugarit\Surveyor\Types\ArrayType;
use Ugarit\Surveyor\Types\StringType;

describe('Enum component', function () {
    it('can be instantiated with name and cases', function () {
        $enum = new Enum('App\\Enums\\Status', [
            'ACTIVE' => 'active',
            'INACTIVE' => 'inactive',
        ]);

        expect($enum->name)->toBe('App\\Enums\\Status');
        expect($enum->cases)->toBe([
            'ACTIVE' => 'active',
            'INACTIVE' => 'inactive',
        ]);
    });

    it('supports HasFilePath trait', function () {
        $enum = new Enum('App\\Enums\\Status', ['ACTIVE' => 'active']);
        $enum->setFilePath('/path/to/Status.php');

        expect($enum->filePath())->toBe('/path/to/Status.php');
    });
});

describe('Model component', function () {
    it('can be instantiated with name', function () {
        $model = new Model('App\\Models\\User');

        expect($model->name)->toBe('App\\Models\\User');
    });

    it('can add and retrieve attributes', function () {
        $model = new Model('App\\Models\\User');
        $type = new StringType;

        $model->addAttribute('name', $type);
        $model->addAttribute('email', $type);

        $attributes = $model->getAttributes();

        expect($attributes)->toHaveKey('name');
        expect($attributes)->toHaveKey('email');
    });

    it('can add and retrieve relations', function () {
        $model = new Model('App\\Models\\User');
        $type = new StringType;

        $model->addRelation('posts', $type);
        $model->addRelation('profile', $type);

        $relations = $model->getRelations();

        expect($relations)->toHaveKey('posts');
        expect($relations)->toHaveKey('profile');
    });
});

describe('BroadcastChannel component', function () {
    it('can be instantiated with name and resolver', function () {
        $callback = fn () => true;
        $channel = new BroadcastChannel('users.{id}', $callback);

        expect($channel->name)->toBe('users.{id}');
        expect($channel->resolvesTo)->toBe($callback);
    });
});

describe('BroadcastEvent component', function () {
    it('can be instantiated with name, className, and data', function () {
        $data = new ArrayType(['user' => new StringType]);
        $event = new BroadcastEvent('user.created', 'App\\Events\\UserCreated', $data);

        expect($event->name)->toBe('user.created');
        expect($event->className)->toBe('App\\Events\\UserCreated');
        expect($event->data)->toBe($data);
    });

    it('supports HasFilePath trait', function () {
        $data = new ArrayType([]);
        $event = new BroadcastEvent('user.created', 'App\\Events\\UserCreated', $data);
        $event->setFilePath('/path/to/UserCreated.php');

        expect($event->filePath())->toBe('/path/to/UserCreated.php');
    });
});

describe('EnvironmentVariable component', function () {
    it('can be instantiated with key and string value', function () {
        $env = new EnvironmentVariable('APP_NAME', 'Ugarit');

        expect($env->key)->toBe('APP_NAME');
        expect($env->value)->toBe('Ugarit');
    });

    it('can hold integer values', function () {
        $env = new EnvironmentVariable('CACHE_TTL', 3600);

        expect($env->key)->toBe('CACHE_TTL');
        expect($env->value)->toBe(3600);
    });

    it('can hold boolean values', function () {
        $env = new EnvironmentVariable('APP_DEBUG', true);

        expect($env->key)->toBe('APP_DEBUG');
        expect($env->value)->toBeTrue();
    });

    it('can hold null values', function () {
        $env = new EnvironmentVariable('OPTIONAL_KEY', null);

        expect($env->key)->toBe('OPTIONAL_KEY');
        expect($env->value)->toBeNull();
    });
});

describe('InertiaResponse component', function () {
    it('can be instantiated with component and data', function () {
        $response = new InertiaResponse('Pages/Dashboard', [
            'user' => new StringType,
        ]);

        expect($response->component)->toBe('Pages/Dashboard');
        expect($response->data)->toHaveKey('user');
    });
});

describe('InertiaSharedData component', function () {
    it('can be instantiated with data', function () {
        $data = new ArrayType(['auth' => new StringType]);
        $shared = new InertiaSharedData($data);

        expect($shared->data)->toBe($data);
    });
});

describe('JsonResponse component', function () {
    it('can be instantiated with data array', function () {
        $data = ['status' => 'success', 'message' => 'Created'];
        $response = new JsonResponse($data);

        expect($response->data)->toBe($data);
    });
});

describe('ResourceResponse component', function () {
    it('can be instantiated with full attributes', function () {
        $data = ['id' => new StringType, 'name' => new StringType];
        $additional = ['version' => new StringType];

        $response = new ResourceResponse(
            resourceClass: 'App\\Http\\Resources\\UserResource',
            data: $data,
            isCollection: false,
            wrap: 'data',
            additional: $additional,
        );

        expect($response->resourceClass)->toBe('App\\Http\\Resources\\UserResource');
        expect($response->data)->toBe($data);
        expect($response->isCollection)->toBeFalse();
        expect($response->wrap)->toBe('data');
        expect($response->additional)->toBe($additional);
    });

    it('supports collection and null wrap', function () {
        $response = new ResourceResponse(
            resourceClass: 'App\\Http\\Resources\\UserResource',
            data: ['id' => new StringType],
            isCollection: true,
            wrap: null,
        );

        expect($response->isCollection)->toBeTrue();
        expect($response->wrap)->toBeNull();
        expect($response->additional)->toBe([]);
    });
});

describe('JsonApiResponse component', function () {
    it('can be instantiated with all sections', function () {
        $attributes = ['name' => new StringType];
        $relationships = ['posts' => new StringType];
        $links = ['self' => new StringType];
        $meta = ['count' => new StringType];
        $additional = ['version' => new StringType];

        $response = new JsonApiResponse(
            resourceClass: 'App\\JsonApi\\UserResource',
            attributes: $attributes,
            relationships: $relationships,
            links: $links,
            meta: $meta,
            isCollection: false,
            additional: $additional,
        );

        expect($response->resourceClass)->toBe('App\\JsonApi\\UserResource');
        expect($response->attributes)->toBe($attributes);
        expect($response->relationships)->toBe($relationships);
        expect($response->links)->toBe($links);
        expect($response->meta)->toBe($meta);
        expect($response->isCollection)->toBeFalse();
        expect($response->additional)->toBe($additional);
    });

    it('supports collection responses', function () {
        $response = new JsonApiResponse(
            resourceClass: 'App\\JsonApi\\UserResource',
            attributes: ['name' => new StringType],
            relationships: [],
            links: [],
            meta: [],
            isCollection: true,
        );

        expect($response->isCollection)->toBeTrue();
        expect($response->additional)->toBe([]);
    });
});

describe('Validator component', function () {
    it('can be instantiated with rules', function () {
        $rules = [
            'name' => ['required', 'string'],
            'email' => ['required', 'email'],
        ];
        $validator = new Validator($rules);

        expect($validator->rules)->toBe($rules);
    });
});

describe('Verb component', function () {
    it('normalizes verb to lowercase', function () {
        $verb = new Verb('GET');

        expect($verb->actual)->toBe('get');
    });

    it('sets formSafe to get for GET requests', function () {
        $verb = new Verb('GET');

        expect($verb->formSafe)->toBe('get');
    });

    it('sets formSafe to get for HEAD requests', function () {
        $verb = new Verb('HEAD');

        expect($verb->formSafe)->toBe('get');
    });

    it('sets formSafe to get for OPTIONS requests', function () {
        $verb = new Verb('OPTIONS');

        expect($verb->formSafe)->toBe('get');
    });

    it('sets formSafe to post for POST requests', function () {
        $verb = new Verb('POST');

        expect($verb->formSafe)->toBe('post');
    });

    it('sets formSafe to post for PUT requests', function () {
        $verb = new Verb('PUT');

        expect($verb->formSafe)->toBe('post');
    });

    it('sets formSafe to post for PATCH requests', function () {
        $verb = new Verb('PATCH');

        expect($verb->formSafe)->toBe('post');
    });

    it('sets formSafe to post for DELETE requests', function () {
        $verb = new Verb('DELETE');

        expect($verb->formSafe)->toBe('post');
    });
});
