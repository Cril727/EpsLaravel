<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExpoPushNotificationService
{
    private $expoApiUrl = 'https://exp.host/--/api/v2/push/send';

    /**
     * Send a push notification to a single device
     *
     * @param string $expoPushToken
     * @param string $title
     * @param string $body
     * @param array $data
     * @return bool
     */
    public function sendPushNotification($expoPushToken, $title, $body, $data = [])
    {
        if (empty($expoPushToken) || !$this->isValidExpoPushToken($expoPushToken)) {
            Log::warning('Invalid Expo push token provided', ['token' => $expoPushToken]);
            return false;
        }

        try {
            $message = [
                'to' => $expoPushToken,
                'sound' => 'default',
                'title' => $title,
                'body' => $body,
                'data' => $data,
                'priority' => 'high',
                'channelId' => 'default',
            ];

            $response = Http::post($this->expoApiUrl, $message);

            if ($response->successful()) {
                $result = $response->json();
                
                if (isset($result['data'][0]['status']) && $result['data'][0]['status'] === 'ok') {
                    Log::info('Push notification sent successfully', [
                        'token' => $expoPushToken,
                        'title' => $title
                    ]);
                    return true;
                } else {
                    Log::error('Push notification failed', [
                        'token' => $expoPushToken,
                        'response' => $result
                    ]);
                    return false;
                }
            } else {
                Log::error('Failed to send push notification', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('Exception sending push notification: ' . $e->getMessage(), [
                'token' => $expoPushToken,
                'exception' => $e
            ]);
            return false;
        }
    }

    /**
     * Send push notifications to multiple devices
     *
     * @param array $expoPushTokens
     * @param string $title
     * @param string $body
     * @param array $data
     * @return array
     */
    public function sendPushNotifications($expoPushTokens, $title, $body, $data = [])
    {
        $messages = [];
        
        foreach ($expoPushTokens as $token) {
            if ($this->isValidExpoPushToken($token)) {
                $messages[] = [
                    'to' => $token,
                    'sound' => 'default',
                    'title' => $title,
                    'body' => $body,
                    'data' => $data,
                    'priority' => 'high',
                    'channelId' => 'default',
                ];
            }
        }

        if (empty($messages)) {
            Log::warning('No valid Expo push tokens provided');
            return ['success' => false, 'message' => 'No valid tokens'];
        }

        try {
            $response = Http::post($this->expoApiUrl, $messages);

            if ($response->successful()) {
                $result = $response->json();
                Log::info('Batch push notifications sent', [
                    'count' => count($messages),
                    'response' => $result
                ]);
                return ['success' => true, 'data' => $result];
            } else {
                Log::error('Failed to send batch push notifications', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return ['success' => false, 'message' => 'Failed to send notifications'];
            }
        } catch (\Exception $e) {
            Log::error('Exception sending batch push notifications: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Send appointment notification
     *
     * @param string $expoPushToken
     * @param array $appointmentData
     * @return bool
     */
    public function sendAppointmentNotification($expoPushToken, $appointmentData)
    {
        $title = '🏥 Nueva Cita Médica';
        $body = sprintf(
            'Tienes una cita el %s con %s',
            date('d/m/Y H:i', strtotime($appointmentData['fechaHora'])),
            $appointmentData['doctor_name'] ?? 'el doctor'
        );

        $data = [
            'type' => 'appointment',
            'appointmentId' => $appointmentData['id'] ?? null,
            'screen' => 'Appointments',
        ];

        return $this->sendPushNotification($expoPushToken, $title, $body, $data);
    }

    /**
     * Send appointment reminder notification
     *
     * @param string $expoPushToken
     * @param array $appointmentData
     * @return bool
     */
    public function sendAppointmentReminder($expoPushToken, $appointmentData)
    {
        $title = '⏰ Recordatorio de Cita';
        $body = sprintf(
            'Tu cita con %s es en 1 hora en %s',
            $appointmentData['doctor_name'] ?? 'el doctor',
            $appointmentData['consultorio_name'] ?? 'el consultorio'
        );

        $data = [
            'type' => 'appointment_reminder',
            'appointmentId' => $appointmentData['id'] ?? null,
            'screen' => 'Appointments',
        ];

        return $this->sendPushNotification($expoPushToken, $title, $body, $data);
    }

    /**
     * Send appointment status update notification
     *
     * @param string $expoPushToken
     * @param string $status
     * @param array $appointmentData
     * @return bool
     */
    public function sendAppointmentStatusUpdate($expoPushToken, $status, $appointmentData)
    {
        $statusMessages = [
            'Aprobada' => '✅ Tu cita ha sido aprobada',
            'Rechazada' => '❌ Tu cita ha sido rechazada',
            'Cancelada' => '🚫 Tu cita ha sido cancelada',
            'Completada' => '✔️ Tu cita ha sido completada',
        ];

        $title = $statusMessages[$status] ?? '📋 Actualización de Cita';
        $body = sprintf(
            'Cita del %s - Estado: %s',
            date('d/m/Y H:i', strtotime($appointmentData['fechaHora'])),
            $status
        );

        $data = [
            'type' => 'appointment_status_update',
            'appointmentId' => $appointmentData['id'] ?? null,
            'status' => $status,
            'screen' => 'Appointments',
        ];

        return $this->sendPushNotification($expoPushToken, $title, $body, $data);
    }

    /**
     * Send password change notification
     *
     * @param string $expoPushToken
     * @return bool
     */
    public function sendPasswordChangeNotification($expoPushToken)
    {
        $title = '🔐 Contraseña Actualizada';
        $body = 'Tu contraseña ha sido cambiada exitosamente. Si no fuiste tú, contacta al administrador inmediatamente.';

        $data = [
            'type' => 'password_change',
            'screen' => 'Profile',
        ];

        return $this->sendPushNotification($expoPushToken, $title, $body, $data);
    }

    /**
     * Validate Expo push token format
     *
     * @param string $token
     * @return bool
     */
    private function isValidExpoPushToken($token)
    {
        // Expo push tokens start with ExponentPushToken[
        return !empty($token) && (
            str_starts_with($token, 'ExponentPushToken[') ||
            str_starts_with($token, 'ExpoPushToken[')
        );
    }
}