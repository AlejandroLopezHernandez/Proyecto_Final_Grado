<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Process\Process;
use Symfony\Component\Routing\Annotation\Route;

class CartaController extends AbstractController
{
    #[Route('/carta', name: 'ver_carta')]
    public function VerCarta(): Response
    {
        $rutaArchivo = $this->getParameter('kernel.project_dir') . '/public/IndexCarta.html';

        if (!file_exists($rutaArchivo)) {
            return new Response('<h1>La carta aún no ha sido generada</h1><p>Ejecuta el script <code>generar_carta.py</code> primero.</p>', 404);
        }

        return new Response(file_get_contents($rutaArchivo));
    }

    #[Route('/carta/regenerar', name: 'carta_regenerar')]
    public function regenerarCarta(): Response
    {
        $scriptPath = $this->getParameter('kernel.project_dir') . '/public/generar_carta.py';
        $htmlPath = $this->getParameter('kernel.project_dir') . '/public/IndexCarta.html';

        // Eliminar archivo viejo si existe
        if (file_exists($htmlPath)) {
            unlink($htmlPath);
        }

        // Verificar que existe el script
        if (!file_exists($scriptPath)) {
            return $this->json(['error' => "Script no encontrado: $scriptPath"], 500);
        }

        // Ejecutar script Python
        $process = new Process(['python3', $scriptPath]);
        $process->setWorkingDirectory($this->getParameter('kernel.project_dir') . '/public');
        $process->setTimeout(60);

        $process->run();

        $exitCode = $process->getExitCode();
        $errorOutput = $process->getErrorOutput();

        if ($exitCode !== 0) {
            return $this->json([
                'error' => 'Error ejecutando Python',
                'exit_code' => $exitCode,
                'error_output' => $errorOutput
            ], 500);
        }

        // Verificar que se generó el archivo
        if (!file_exists($htmlPath)) {
            return $this->json(['error' => 'Archivo HTML no fue generado'], 500);
        }

        return $this->json([
            'success' => true,
            'message' => 'Carta regenerada exitosamente',
            'file_size' => filesize($htmlPath),
            'generated_at' => date('Y-m-d H:i:s', filemtime($htmlPath))
        ]);
    }

    #[Route('/carta/mostrar', name: 'carta_mostrar')]
    public function mostrarCarta(): Response
    {
        $htmlPath = $this->getParameter('kernel.project_dir') . '/public/IndexCarta.html';

        if (!file_exists($htmlPath)) {
            return new Response('Carta no encontrada. <a href="/carta/regenerar">Generar carta</a>', 404);
        }

        $content = file_get_contents($htmlPath);

        if (empty($content)) {
            return new Response('Carta vacía. <a href="/carta/regenerar">Regenerar carta</a>', 500);
        }

        // Agregar timestamp para debugging
        $timestamp = date('Y-m-d H:i:s', filemtime($htmlPath));
        $content = str_replace('</body>', "<!-- Generado: $timestamp --></body>", $content);

        return new Response($content);
    }

    #[Route('/carta/debug', name: 'carta_debug')]
    public function debugCarta(): Response
    {
        $projectDir = $this->getParameter('kernel.project_dir');
        $scriptPath = $projectDir . '/public/generar_carta.py';
        $htmlPath = $projectDir . '/public/IndexCarta.html';
        $rendererPath = $projectDir . '/public/carta_renderer.py';

        $debug = [
            'project_dir' => $projectDir,
            'script_exists' => file_exists($scriptPath),
            'renderer_exists' => file_exists($rendererPath),
            'html_exists' => file_exists($htmlPath),
            'html_size' => file_exists($htmlPath) ? filesize($htmlPath) : 0,
            'html_modified' => file_exists($htmlPath) ? date('Y-m-d H:i:s', filemtime($htmlPath)) : 'N/A',
            'script_modified' => file_exists($scriptPath) ? date('Y-m-d H:i:s', filemtime($scriptPath)) : 'N/A',
        ];

        return $this->json($debug);
    }
}
