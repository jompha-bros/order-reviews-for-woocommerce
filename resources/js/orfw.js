function setCookie(cname, cvalue, seconds) 
{
    var domainName = window.location.hostname;
    const d = new Date();
    d.setTime(d.getTime() + (seconds * 1000));
    let expires = 'expires=' + d.toUTCString();
    document.cookie = cname + '=' + cvalue + ';' + expires + ';path=/;SameSite=lax;domain=' + domainName;
}

function getCookie(name)
{
    const value = '; ' + document.cookie;
    const parts = value.split('; ' + name + '=');
    if ( parts.length === 2 )
        return parts.pop().split(';').shift();
}