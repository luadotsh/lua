<?php

declare(strict_types=1);

namespace App\Mcp\Tools\Link;

use App\Actions\Link\GenerateQrCode;
use App\Actions\Link\GetLink;
use App\Mcp\Concerns\ResolvesWorkspace;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[IsReadOnly]
#[Description('Get the QR code for a link as a PNG image. Scans through it are counted separately from ordinary clicks.')]
class GetLinkQrCodeTool extends Tool
{
    use ResolvesWorkspace;

    /**
     * @return array<string, JsonSchema>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->string()->description('The link id.')->required(),
            'color' => $schema->string()->description('Hex colour for the code, e.g. #7C5CFF. Defaults to black.'),
            'size' => $schema->integer()->description('Width and height in pixels. Defaults to 256.'),
        ];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        $link = GetLink::execute($this->workspace($request), (string) $request->get('id'));

        if (! $link) {
            return Response::error('Link not found in this workspace.');
        }

        $png = GenerateQrCode::execute($link, [
            'color' => $request->get('color'),
            'size' => $request->get('size'),
        ]);

        return Response::image(base64_encode($png), 'image/png');
    }
}
