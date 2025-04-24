arr=['React',
'Vue',
'HTML',
'CSS',
'WWW',
'PHP',
'Python',
'ActiveX',
'address',
'alias',
'anonymous',
'FTP',
'cache',
'captcha',
'browser',
'buffer',
'RGB',
'ROM',
'remote',
'login',
'router',
'telnet',
'TCP'];
cnt=0;
n=3
str=arr[cnt];
word1=[];
var score=0
var qwerty='';




for(var element in str)
        {
            qw=`<t id=${element}>`+str[element]+'</t>'
            word1.push(qw);
        }
        document.getElementById('test').innerHTML=word1.join('');
//document.getElementById('t1').onkeyup=function(){abc()};

var count=0; // Count in Array of Letters
merac=0

function changeGreen(count)
{
document.getElementById(count).style.color='#7cc576';
merac++
console.log(merac)
}

function changeRed(count)
{
    document.getElementById(count).style.color='#ff3939';
}
l2=0;

function check(){
    if(merac==word1.length)
    {
        score++;
        document.getElementById('count').innerHTML=score
        cnt++;
        str=arr[cnt];
        count=0;
        merac=0
        l2=0
        word1=[]
    for(var element in str)
        {
            qw=`<t id=${element}>`+str[element]+'</t>'
            word1.push(qw);
        }
        document.getElementById('test').innerHTML=word1.join('');
        document.getElementById('t1').value=''
    }
}

function abc(){

    word=document.getElementById('t1').value;
    l2++;
    qwerty=word[word.length-1];
    l1=word.length;
    if(l1==count+1)
    {
        if(str[count]===qwerty)
        {
                count++;
                changeGreen(count-1);
        }
        else
        {
            count++;
            changeRed(count-1);
        }
    }
    else
    {
        document.getElementById(count-1).style.color='#4e4e4e';
        count--;
    }
    check();
}

//////////////


var clock = 30;
var countdownId = 0;
        function start() {
            setInterval("countdown()", 1000);
        }
        function countdown(){
            if(clock > 0){
                clock = clock - 1;
                document.getElementById('timerr').innerHTML = clock;
            }
            else {
                //Stop clock
                document.getElementById('test').innerHTML="결과 점수";
                document.getElementById('test').style.fontSize="100px";
                document.getElementById('t1').style.display='none';
                clearInterval(countdownId);
            }
        }
