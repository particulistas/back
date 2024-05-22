<div>
    <h1 style="font-family: 'Haas Grot Text R Web', 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 24px; font-weight: bold;">
        A pedido restaurar su contrseña 
    </h1>
    
    <p style="font-family: 'Haas Grot Text R Web', 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 14px;">
        Necesitamos que verifique su cuenta, presione el siguiente botón
    </p>

    <a href="{{ $verificationUrl }}" style="background-color: #EA4C89; border-radius: 8px; border-style: none; box-sizing: border-box; color: #FFFFFF; cursor: pointer; display: inline-block; font-family: 'Haas Grot Text R Web', 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 14px; font-weight: 500; height: 40px; line-height: 20px; list-style: none; margin: 0; outline: none; padding: 10px 16px; position: relative; text-align: center; text-decoration: none; transition: color 100ms; vertical-align: baseline; user-select: none; -webkit-user-select: none; touch-action: manipulation;">
        Verificar
    </a>

    <p style="font-family: 'Haas Grot Text R Web', 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 14px;">
        o copie y pegue el siguiente link en su navegador:
    </p>

    <p style="font-family: 'Haas Grot Text R Web', 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 14px;">
        <a href="{{ $verificationUrl }}" style="color: #EA4C89;">{{ $verificationUrl }}</a>
    </p>
</div>
