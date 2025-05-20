<?php

namespace App\Services;

use Prism\Prism\Schema\ArraySchema;
use Prism\Prism\Schema\ObjectSchema;
use Prism\Prism\Schema\StringSchema;

class SchemaService
{
    public function getResponseSchema(): ObjectSchema
    {
        return new ObjectSchema(
            name: 'ai_response',
            description: 'A structured response with potential tasks',
            properties: [
                new StringSchema('response', 'The conversational response to the user'),
                new ArraySchema(
                    'tasks',
                    'Any tasks identified in the conversation',
                    new ObjectSchema(
                        'task',
                        'A single task item',
                        [
                            new StringSchema('title', 'Task title'),
                            new StringSchema('description', 'Task description', nullable: true),
                            new StringSchema('due_date', 'When the task is due', nullable: true),
                            new StringSchema('priority', 'Task priority (low, medium, high)', nullable: true),
                        ],
                        requiredFields: ['title']
                    )
                ),
            ],
            requiredFields: ['response']
        );
    }
}
