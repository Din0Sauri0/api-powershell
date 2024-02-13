<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePasswordRequest;
use Illuminate\Http\Request;
use Throwable;

class UsuarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UpdatePasswordRequest $request)
    {
        try{

            if($request->validated()){
                $admin_user = $request->admin_user;
                $admin_pass = $request->admin_password;
                $ip = $request->host;
                $target_user = $request->user;
                $new_pass = $request->password;

                // Escapar los argumentos del shell
                $admin_user = escapeshellarg($admin_user);
                $admin_pass = escapeshellarg($admin_pass);
                $ip = escapeshellarg($ip);
                $target_user = escapeshellarg($target_user);
                $new_pass = escapeshellarg($new_pass);

                // Construir el comando de PowerShell
                $command = "powershell -Command \"";
                $command .= "\$securePass = ConvertTo-SecureString -String $admin_pass -AsPlainText -Force; ";
                $command .= "\$cred = New-Object -TypeName System.Management.Automation.PSCredential -ArgumentList $admin_user, \$securePass; ";
                $command .= "Invoke-Command -ComputerName $ip -Credential \$cred -ScriptBlock { ";
                $command .= "param(\$targetUser, \$newPass); ";
                $command .= "Set-LocalUser -Name \$targetUser -Password (ConvertTo-SecureString -AsPlainText \$newPass -Force); ";
                $command .= "} -ArgumentList $target_user, $new_pass; ";
                $command .= "\"";

                // Ejecutar el comando de PowerShell y obtener la salida
                $output = shell_exec($command);

                // Mostrar la salida
                // echo "<pre>$output</pre>";
                return response()->json([
                    'res' => true,
                    'msg' => 'Password actualizada',
                    'resultado_consola' => $output
                ], 200);
            }
        }catch(Throwable $err){
            return response()->json(["Error" => $err->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePasswordRequest $request)
    {



    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}