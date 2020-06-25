r"""
This program contains class for generating fiscal code.
The program generates fiscal code by binding with the C++ DLL file
which must be provided during initialization of the class.
"""


from ctypes import cdll, create_string_buffer, byref
import base64
import sys, os, getopt


class FiscalCode:
    """This class contains 'get_fiscal_code' method for generating fiscal code.
    C++ DLL file which must be provided during initialization of the class.

    When the class is initialized, a 20 bytes buffer is created in memory which the 'get_fiscal_code' method
    uses as storage for fiscal code calculation.
    """
    def __init__(self, dll_file):

        """:param dll_file: DLL file location
        """

        if not os.path.exists(dll_file):
            raise Exception('DLL file cannot be found')

        self.my_dll = cdll.LoadLibrary(dll_file)
        self.fiscal_code = create_string_buffer(b'\000' * 20)  # create 20-bytes buffer in memory

    def get_fiscal_code(self, tpin, inv_code, inv_num, inv_time, terminal_id, amount, pri_key) -> bytes:
        """Calculates fiscal code

        :param tpin: business TPIN (type: string)
        :param inv_code: invoice code (type: string)
        :param inv_num: invoice number (type: string)
        :param inv_time: invoicing time. this should be Zambia local time. (type: string)
        :param terminal_id:terminal ID (type: string)
        :param amount: amount  (type: string)
        :param pri_key: private key (type: string)
        :return: fiscal code bytes
        """
        if len(tpin) != 18 or len(terminal_id) != 12 or len(inv_code) != 12 or \
                len(inv_num) != 8 or len(inv_time) != 14 or len(amount) != 20:
            raise Exception("Invalid length for an input argument")

        pri_key_b64decode = base64.b64decode(pri_key)

        self.my_dll.GetFiscalCode(tpin.encode(), inv_code.encode(), inv_num.encode(), inv_time.encode(),
                                  terminal_id.encode(), amount.encode(), byref(self.fiscal_code),
                                  pri_key_b64decode,
                                  len(pri_key_b64decode))

        return self.fiscal_code.value

def main(argv):
    try:
        opts, args = getopt.getopt(argv,"t:c:n:u:i:a:k:")
        opts = dict( opts )

        tpin = opts['-t']
        inv_code = opts['-c']
        inv_num = opts['-n']
        inv_time = opts['-u']
        terminal_id = opts['-i']
        amount = opts['-a']
        pri_key = opts['-k']

    except Exception:
        print("error")
        sys.exit(1)

    dir_path = os.path.dirname(os.path.realpath(__file__))
    os.chdir( dir_path );

    fiscal = FiscalCode("FiscalCode.dll")
    print( fiscal.get_fiscal_code( tpin, inv_code, inv_num, inv_time, terminal_id, amount, pri_key ).decode() )

if __name__ == "__main__":
    main(sys.argv[1:])
