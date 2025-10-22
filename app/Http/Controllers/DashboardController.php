<?php

namespace App\Http\Controllers;

use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        return inertia(
            component: 'dashboard',
            props: [
                'stats'     => $this->getStats(),
                'chartData' => $this->getChartData(),
                'logs'      => $this->getLogs(),
            ]);
    }

    /**
     * TODO: Replace with real stats
     */
    private function getStats(): array
    {
        return [
            'functions'        => 3,
            'totalRuns'        => 126,
            'runsToday'        => 21,
            'unresolvedErrors' => 0,
        ];
    }

    private function getChartData(): array
    {
        return [
            '24h' => [
                ['time' => '00:00', 'success' => 10, 'errors' => 2],
                ['time' => '02:00', 'success' => 7, 'errors' => 1],
                ['time' => '04:00', 'success' => 4, 'errors' => 1],
                ['time' => '06:00', 'success' => 13, 'errors' => 2],
                ['time' => '08:00', 'success' => 25, 'errors' => 3],
                ['time' => '10:00', 'success' => 32, 'errors' => 3],
                ['time' => '12:00', 'success' => 38, 'errors' => 4],
                ['time' => '14:00', 'success' => 34, 'errors' => 4],
                ['time' => '16:00', 'success' => 40, 'errors' => 5],
                ['time' => '18:00', 'success' => 28, 'errors' => 4],
                ['time' => '20:00', 'success' => 22, 'errors' => 3],
                ['time' => '22:00', 'success' => 15, 'errors' => 3],
            ],
            '7d' => [
                ['time' => 'Mon', 'success' => 130, 'errors' => 15],
                ['time' => 'Tue', 'success' => 120, 'errors' => 12],
                ['time' => 'Wed', 'success' => 155, 'errors' => 13],
                ['time' => 'Thu', 'success' => 142, 'errors' => 12],
                ['time' => 'Fri', 'success' => 175, 'errors' => 14],
                ['time' => 'Sat', 'success' => 88, 'errors' => 10],
                ['time' => 'Sun', 'success' => 78, 'errors' => 9],
            ],
            '30d' => [
                ['time' => 'Week 1', 'success' => 800, 'errors' => 56],
                ['time' => 'Week 2', 'success' => 870, 'errors' => 53],
                ['time' => 'Week 3', 'success' => 990, 'errors' => 55],
                ['time' => 'Week 4', 'success' => 930, 'errors' => 57],
            ],
        ];
    }

    private function getLogs(): array
    {
        return [
            [
                'id'           => '9d3e5f8a-7c2b-4a1d-9e5f-8a7c2b4a1d9e',
                'title'        => 'POST /run/plex-activity-webhook',
                'functionName' => 'plex-webhook',
                'runtime'      => 'node-20',
                'status'       => 'success',
                'exitCode'     => 0,
                'startTime'    => '2025-10-11T19:29:20.001Z',
                'metrics'      => [
                    'responseTime'  => '45ms',
                    'executionTime' => '6.1s',
                    'maxMemory'     => '128 MB',
                    'cost'          => '$0.0012',
                ],
                'entries' => [
                    ['timestamp' => '2025-10-11T19:29:20.001Z', 'message' => '[DEBUG] Initializing container provisioner'],
                    ['timestamp' => '2025-10-11T19:29:20.234Z', 'message' => '[DEBUG] Loading configuration from /etc/dysfunctional/config.yml'],
                    ['timestamp' => '2025-10-11T19:29:20.567Z', 'message' => '[INFO] Starting container provisioning process'],
                    ['timestamp' => '2025-10-11T19:29:21.123Z', 'message' => '[INFO] Container started successfully'],
                    ['timestamp' => '2025-10-11T19:29:21.456Z', 'message' => '[INFO] Provisioning Docker containers as functions'],
                    ['timestamp' => '2025-10-11T19:29:22.001Z', 'message' => '[NOTICE] Active version: musing_solomon1 | Build: 2.3s'],
                    ['timestamp' => '2025-10-11T19:29:22.234Z', 'message' => '[INFO] Used by: 0 developers | Created by: dprevite'],
                    ['timestamp' => '2025-10-11T19:29:22.567Z', 'message' => '[DEBUG] Checking Docker daemon availability'],
                    ['timestamp' => '2025-10-11T19:29:23.001Z', 'message' => '[INFO] Connecting to Docker daemon at unix:///var/run/docker.sock'],
                    ['timestamp' => '2025-10-11T19:29:23.345Z', 'message' => '[INFO] Docker daemon connected successfully'],
                    ['timestamp' => '2025-10-11T19:29:23.678Z', 'message' => '[INFO] Pulling image: node:20-alpine'],
                    ['timestamp' => '2025-10-11T19:29:23.890Z', 'message' => '[DEBUG] Image pull progress: 23%'],
                    ['timestamp' => '2025-10-11T19:29:24.123Z', 'message' => '[INFO] Image pulled successfully (sha256:abc123def456)'],
                    ['timestamp' => '2025-10-11T19:29:24.456Z', 'message' => '[INFO] Creating container with id: abc123def456'],
                    ['timestamp' => '2025-10-11T19:29:24.789Z', 'message' => '[INFO] Container created successfully'],
                    ['timestamp' => '2025-10-11T19:29:25.001Z', 'message' => '[INFO] Starting container...'],
                    ['timestamp' => '2025-10-11T19:29:25.234Z', 'message' => '[SUCCESS] Container is now running'],
                    ['timestamp' => '2025-10-11T19:29:25.567Z', 'message' => '[INFO] Health check passed'],
                    ['timestamp' => '2025-10-11T19:29:25.890Z', 'message' => '[INFO] Function ready to accept requests on port 3000'],
                    ['timestamp' => '2025-10-11T19:29:26.123Z', 'message' => '[DEBUG] Registering function endpoint: /api/docker-provision'],
                ],
            ],
            [
                'id'           => 'a1b2c3d4-e5f6-4789-abcd-ef0123456789',
                'title'        => 'GET /run/chatbot-fetch-whats-playing',
                'functionName' => 'chatbot-media',
                'runtime'      => 'python-3.11',
                'status'       => 'running',
                'startTime'    => '2025-10-11T19:30:00.789Z',
                'metrics'      => [
                    'responseTime'  => '1.2s',
                    'executionTime' => '3.8s',
                    'maxMemory'     => '256 MB',
                    'cost'          => '$0.0018',
                ],
                'entries' => [
                    ['timestamp' => '2025-10-11T19:30:00.789Z', 'message' => '[DEBUG] Initializing image processor worker'],
                    ['timestamp' => '2025-10-11T19:30:01.123Z', 'message' => '[INFO] Starting image processing pipeline'],
                    ['timestamp' => '2025-10-11T19:30:01.456Z', 'message' => '[INFO] Loading image from S3: images/photo.jpg'],
                    ['timestamp' => '2025-10-11T19:30:01.789Z', 'message' => '[DEBUG] S3 GetObject request initiated'],
                    ['timestamp' => '2025-10-11T19:30:02.001Z', 'message' => '[INFO] Image loaded: 4032x3024 pixels (12.2 MB)'],
                    ['timestamp' => '2025-10-11T19:30:02.234Z', 'message' => '[INFO] Applying thumbnail transformation'],
                    ['timestamp' => '2025-10-11T19:30:02.456Z', 'message' => '[DEBUG] Allocating memory buffer: 12.2 MB'],
                    ['timestamp' => '2025-10-11T19:30:02.567Z', 'message' => '[INFO] Resizing to 800x600'],
                    ['timestamp' => '2025-10-11T19:30:02.890Z', 'message' => '[DEBUG] Using Lanczos3 interpolation algorithm'],
                    ['timestamp' => '2025-10-11T19:30:03.001Z', 'message' => '[INFO] Optimizing image quality'],
                    ['timestamp' => '2025-10-11T19:30:03.234Z', 'message' => '[DEBUG] Processing... 35% complete'],
                    ['timestamp' => '2025-10-11T19:30:03.567Z', 'message' => '[DEBUG] Processing... 68% complete'],
                    ['timestamp' => '2025-10-11T19:30:03.890Z', 'message' => '[INFO] Processing... 85% complete'],
                    ['timestamp' => '2025-10-11T19:30:04.123Z', 'message' => '[WARNING] High memory usage detected: 89%'],
                ],
            ],
            [
                'id'           => 'f7e8d9c0-b1a2-4f3e-8d9c-0b1a2f3e8d9c',
                'title'        => 'GET /run/resize-image?width=1500px&src=IMG123.jpg&format=png',
                'functionName' => 'resize-image',
                'runtime'      => 'go-1.21',
                'status'       => 'error',
                'exitCode'     => 1,
                'startTime'    => '2025-10-11T19:28:00.890Z',
                'metrics'      => [
                    'responseTime'  => '523ms',
                    'executionTime' => '2.3s',
                    'maxMemory'     => '64 MB',
                    'cost'          => '$0.0008',
                ],
                'entries' => [
                    ['timestamp' => '2025-10-11T19:28:00.890Z', 'message' => '[DEBUG] Database migration worker started'],
                    ['timestamp' => '2025-10-11T19:28:01.123Z', 'message' => '[INFO] Starting database migration'],
                    ['timestamp' => '2025-10-11T19:28:01.456Z', 'message' => '[INFO] Connecting to database: postgres://prod-db:5432/dysfunctional'],
                    ['timestamp' => '2025-10-11T19:28:01.789Z', 'message' => '[DEBUG] Connection pool initialized with max 10 connections'],
                    ['timestamp' => '2025-10-11T19:28:02.001Z', 'message' => '[INFO] Connection established'],
                    ['timestamp' => '2025-10-11T19:28:02.234Z', 'message' => '[INFO] Reading migration files from ./migrations'],
                    ['timestamp' => '2025-10-11T19:28:02.567Z', 'message' => '[INFO] Found 3 pending migrations'],
                    ['timestamp' => '2025-10-11T19:28:02.890Z', 'message' => '[DEBUG] Beginning transaction'],
                    ['timestamp' => '2025-10-11T19:28:03.001Z', 'message' => '[INFO] Running migration: 001_create_users_table.sql'],
                    ['timestamp' => '2025-10-11T19:28:03.345Z', 'message' => '[INFO] Migration 001 completed successfully'],
                    ['timestamp' => '2025-10-11T19:28:03.567Z', 'message' => '[NOTICE] Created table: users (5 columns)'],
                    ['timestamp' => '2025-10-11T19:28:03.678Z', 'message' => '[INFO] Running migration: 002_add_email_column.sql'],
                    ['timestamp' => '2025-10-11T19:28:03.890Z', 'message' => '[DEBUG] Executing: ALTER TABLE users ADD COLUMN email VARCHAR(255)'],
                    ['timestamp' => '2025-10-11T19:28:04.123Z', 'message' => "[ERROR] Duplicate column name 'email' in table 'users'"],
                    ['timestamp' => '2025-10-11T19:28:04.234Z', 'message' => '[ERROR] Query failed: column "email" of relation "users" already exists'],
                    ['timestamp' => '2025-10-11T19:28:04.456Z', 'message' => '[ERROR] Migration failed: Constraint violation'],
                    ['timestamp' => '2025-10-11T19:28:04.567Z', 'message' => '[WARNING] Rolling back transaction...'],
                    ['timestamp' => '2025-10-11T19:28:04.789Z', 'message' => '[ERROR] Rollback completed'],
                    ['timestamp' => '2025-10-11T19:28:05.001Z', 'message' => '[CRITICAL] Process exited with code 1'],
                ],
            ],
        ];
    }
}
