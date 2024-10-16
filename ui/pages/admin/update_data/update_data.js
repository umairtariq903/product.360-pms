function UpdateData()
{
    let data = GetFileUploadData("fileUpload1");
    if(typeof data.Yol == "undefined" || data.Yol == "")
    {
        Page.ShowError("File Upload!!");
        return;
    }
    Page.Ajax.SendBool("UpdateData", data, function () {
        Page.ShowSuccess("Process successfully completed");

    });
}
