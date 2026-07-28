#include <assert.h>

#include "../components/drivers/tds_sensor/include/tds_uart_parser.h"

int main() {
    TdsUartParser parser;
    int tds = 0;

    parser.push("TDS: 245.7 ppm\r\n");
    assert(parser.takeReading(tds));
    assert(tds == 246);

    parser.push("invalid\n");
    assert(!parser.takeReading(tds));

    parser.push("300\n");
    assert(parser.takeReading(tds));
    assert(tds == 300);

    return 0;
}
