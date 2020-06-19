using System.Runtime.InteropServices;

public static class DllHelper
{
    [DllImport("FiscalCode.dll", CharSet = CharSet.Ansi, CallingConvention = CallingConvention.Cdecl, EntryPoint = "GetFiscalCode")]
    public static extern int GetFiscalCode(string BPN, string code, string number, string date, string terminalID, string amount, [MarshalAs(UnmanagedType.LPStr)] StringBuilder fiscalCode, string priKey, int keyLen);
}

private void main(string[] args)
{
    try
    {
        Console.WriteLine( DllHelper.GetFiscalCode("023456789012345678", "137011650142", "03729543", "20170706201500", "123456789012", "00000000000001991.00",fiscalCode, "1234567890", 10) );
    }
    catch (Exception ex)
    {
        Console.WriteLine(ex.Message);
    }
}
