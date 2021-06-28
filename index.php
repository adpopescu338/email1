<?php
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fake Emailer</title>
    <style>
    html{
        overflow-x:hidden;
        padding:0;
        margin:0
    }
    body{
        background-color: #7FDBFF;
        color: #001f3f;
        width:99vw;
        overflow-x:hidden;
        padding:0;
        margin:0
    }
    dialog{
        background-color: white;
        border-bottom-left-radius: 30px;
        border-bottom-right-radius: 30px;
        border-top-left-radius: 0px;
        border-top-right-radius: 30px;
        text-align: center;
        box-shadow: 0 0 5px black;
        border: 1px solid #001f3f;
        color: #001f3f;

    }
    #submit{
        font-size:xx-large;
        border-radius: 6px;
        box-shadow: 0 0 6px black;
    }
    input{
        padding: 4px 8px 4px 8px;
        font-size: 16px;
        font-weight: 600;
        border-bottom-left-radius: 10px;
        border-bottom-right-radius: 10px;
        border-top-left-radius: 0px;
        border-top-right-radius: 10px;
        box-shadow: 0 0 5px white;
    }
    textarea{
        padding: 4px 4px 4px 4px;
        font-size: 14px;
        font-weight: 600;
        border-bottom-left-radius: 10px;
        border-bottom-right-radius: 10px;
        border-top-left-radius: 0px;
        border-top-right-radius: 10px;
        box-shadow: 0 0 5px white;
        width: calc(280px + 8vw);
    }
    #ml{
        position:fixed;
        bottom:0;
        left: calc(50vw - 100px)
    }
    [disabled]{
        opacity:.5
    }
    </style>
</head>
<body style='text-align:center; margin-top: 20px'>
    <form>
    <label>From</label> <br>
    <input id='from'> <br> <br>

    <label>To</label> <br>
    <input id='to'> <br> <br>

    <label>Subject</label> <br>
    <input id='subject'> <br> <br>

    <label>Text</label> <br>
    <textarea id="message" rows="7" placeholder='You can also write HTML: <b>hello</b> this will produce a bold hello. Try it and see the output below'></textarea>
    <div id='ht' style='min-height:15px;word-wrap:wrap'></div>

    <label>Attachment: &nbsp &nbsp &nbsp
    <input type="file" id="file" multiple/></label>
    <br> <br> <br>
    <input id='submit' type="submit" value='Submit'>
    </form>
    <dialog id='modal'></dialog>
    <br> <br>
    <a href='mailto:fake.emailer.app@gmail.com' id='ml'>fake.emailer.app@gmail.com</a>
    
    <script>
    form = document.getElementsByTagName('form')[0]
    submit = document.getElementById('submit')
    from = document.getElementById('from')
    to = document.getElementById('to')
    subject = document.getElementById('subject')
    message = document.getElementById('message')
    modal = document.getElementById('modal')
    file  = document.getElementById('file')


    window.onclick = (event)=>{
                if(modal.open &&event.target.id!='submit'){
                    modal.close()
                }
            }

            message.oninput = ()=>{
                if(message.value.includes('<') && message.value.includes('>')){
                    document.getElementById('ht').innerHTML = message.value
                }
            }


from.oninput = to.oninput = function (){
    this.value = this.value.replace(/ /g, '')
    if(ValidateEmail(this.value)){
        this.style.borderColor = ''
        this.style.borderWidth = ''
    }

    if(!from.style.borderColor && !to.style.borderColor){
        submit.disabled = false
    }
}

from.onblur = to.onblur = function (){
    if(!ValidateEmail(this.value)){
        this.style.borderColor = 'red'
        this.style.borderWidth = '3px'
    }  else{
        this.style.borderColor = ''
        this.style.borderWidth = ''
    }
    
    if(from.style.borderColor == 'red' || to.style.borderColor == 'red' ){
        submit.disabled = true
    } else {
        submit.disabled = false
    }
}

    submit.onclick = (e)=>{
        e.preventDefault()
        if(from.value.includes('@yahoo.')){
            modal.innerHTML = `<h2>Sorry, but we can't send from a yahoo account &#128543</h2>`
            modal.showModal()
            setTimeout(() => {
                modal.close()
            }, 5000);
            return
        } else if (!from.value || !to.value){
            modal.innerHTML = `<h2>Fill in the fields first</h2>`
            modal.showModal()
            setTimeout(() => {
                modal.close()
            }, 5000);
            return
        } 
        submit.disabled = true

        let data = new FormData()
        data.append('to', to.value)
        data.append('from', from.value)
        data.append('subject', subject.value)
        data.append('message', message.value)
        for (let i = 0; i<file.files.length; i++){
            data.append(file.files[i].name, file.files[i])
        }



        fetch(`send.php`, {
            method : 'POST',
            body : data
        })
        .then(r=>r.text())
        .then(r=>{
            console.log(r)
            submit.disabled = false;
            if(r=='OK'){
                modal.innerHTML = '<h2>Email sent &#128520</h2>'
            } else {
                modal.innerHTML = '<h2>Sorry, something went wrong &#128543</h2>';
            }
           
            modal.showModal()
            setTimeout(() => {
                modal.close()
            }, 5000);
        })

    }

    !function check_if_first_time_and_show_disclaimer(){
        if(!localStorage.getItem('emailer1')){
            localStorage.setItem('emailer1', 'true')
            modal.innerHTML = `
            <h2>Welcome to Fake Emailer</h2>
            <br>
            <p style='color:red; background-color:white'>Use responsibly: 
            If you want to use this app for a prank or other inoffensive activities that's fine, but if you're thinking about phishing emails, then contact <a href='mailto:fake.emailer.app@gmail.com'>fake.emailer.app@gmail.com</a> for a refund
            </p>
            <br>
            <p>Disclaimer <br>
            Some domains use DMIK to prevent email spoofing, therefore this app cannot send emails FROM those domains. <br>
            For example, you can't send email from yahoo, but you can still send emails to an yahoo account. 
            <br>
            <br>
            <h2>click on the screen to close pop-up</h2>
            `
            modal.showModal()


        } else {
            modal.innerHTML = '<h2>Welcome!</h2><h2>Use responsibly</h2>'
            modal.showModal()
            setTimeout(() => {
                modal.close()
            }, 3000);
        }
    }()

    function ValidateEmail(mail) {
       if (/^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/.test(mail)){
          return (true)
       }
      return (false)
    }
    </script>
</body>
</html>