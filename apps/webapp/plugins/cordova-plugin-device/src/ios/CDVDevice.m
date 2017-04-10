/*
 Licensed to the Apache Software Foundation (ASF) under one
 or more contributor license agreements.  See the NOTICE file
 distributed with this work for additional information
 regarding copyright ownership.  The ASF licenses this file
 to you under the Apache License, Version 2.0 (the
 "License"); you may not use this file except in compliance
 with the License.  You may obtain a copy of the License at

 http://www.apache.org/licenses/LICENSE-2.0

 Unless required by applicable law or agreed to in writing,
 software distributed under the License is distributed on an
 "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
 KIND, either express or implied.  See the License for the
 specific language governing permissions and limitations
 under the License.
 */

#include <sys/types.h>
#include <sys/sysctl.h>

#import <Cordova/CDV.h>
#import "CDVDevice.h"
//#import <CoreTelephony/CTTelephonyNetworkInfo.h>
//#import <CoreTelephony/CTCarrier.h>

@implementation UIDevice (ModelVersion)

- (NSString*)modelVersion
{
    size_t size;

    sysctlbyname("hw.machine", NULL, &size, NULL, 0);
    char* machine = malloc(size);
    sysctlbyname("hw.machine", machine, &size, NULL, 0);
    NSString* platform = [NSString stringWithUTF8String:machine];
    free(machine);

    return platform;
}

@end

@interface CDVDevice () {}
@end

@implementation CDVDevice

- (NSString*)uniqueAppInstanceIdentifier:(UIDevice*)device
{
    NSUserDefaults* userDefaults = [NSUserDefaults standardUserDefaults];
    static NSString* UUID_KEY = @"CDVUUID";

    NSString* app_uuid = [userDefaults stringForKey:UUID_KEY];

    if (app_uuid == nil) {
        CFUUIDRef uuidRef = CFUUIDCreate(kCFAllocatorDefault);
        CFStringRef uuidString = CFUUIDCreateString(kCFAllocatorDefault, uuidRef);

        app_uuid = [NSString stringWithString:(__bridge NSString*)uuidString];
        [userDefaults setObject:app_uuid forKey:UUID_KEY];
        [userDefaults synchronize];

        CFRelease(uuidString);
        CFRelease(uuidRef);
    }

    return app_uuid;
}

- (void)getDeviceInfo:(CDVInvokedUrlCommand*)command
{
    NSDictionary* deviceProperties = [self deviceProperties];
    CDVPluginResult* pluginResult = [CDVPluginResult resultWithStatus:CDVCommandStatus_OK messageAsDictionary:deviceProperties];

    [self.commandDelegate sendPluginResult:pluginResult callbackId:command.callbackId];
}

- (NSDictionary*)deviceProperties
{
    UIDevice* device = [UIDevice currentDevice];
    NSMutableDictionary* devProps = [NSMutableDictionary dictionaryWithCapacity:4];

    [devProps setObject:@"Apple" forKey:@"manufacturer"];
    [devProps setObject:[device modelVersion] forKey:@"model"];
    [devProps setObject:@"iOS" forKey:@"platform"];
    [devProps setObject:[device systemVersion] forKey:@"version"];
    [devProps setObject:[self uniqueAppInstanceIdentifier:device] forKey:@"uuid"];
    [devProps setObject:[[self class] cordovaVersion] forKey:@"cordova"];
    [devProps setObject:[self getDimension] forKey:@"dm"];
    [devProps setObject:[self getNm] forKey:@"nm"];
    [devProps setObject:@"" forKey:@"mno"];
    [devProps setObject:[self uniqueAppInstanceIdentifier:device] forKey:@"did"];


    NSDictionary* devReturn = [NSDictionary dictionaryWithDictionary:devProps];
    return devReturn;
}

+ (NSString*)cordovaVersion
{
    return CDV_VERSION;
}

//获取屏幕分辨率
- (NSString *)getDimension {
    UIScreen *screen = [UIScreen mainScreen];
    CGSize size = screen.bounds.size;
    CGFloat scale = screen.scale;
    NSInteger width = size.width * scale;
    NSInteger height = size.height * scale;
    NSString *dimension = [NSString stringWithFormat:@"%d*%d",width,height];
    return dimension;
}

//获取联网方式
- (NSString *)getNm {
    UIApplication *app = [UIApplication sharedApplication];
    NSArray *children = [[[app valueForKeyPath:@"statusBar"]valueForKeyPath:@"foregroundView"]subviews];
    NSString *state = @"无";
    int netType = 0;
    //获取到网络返回码
    for (id child in children) {
        if ([child isKindOfClass:NSClassFromString(@"UIStatusBarDataNetworkItemView")]) {
            //获取到状态栏
            netType = [[child valueForKeyPath:@"dataNetworkType"]intValue];

            switch (netType) {
                case 1:
                    state = @"2G";
                    break;
                case 2:
                    state = @"3G";
                    break;
                case 3:
                    state = @"4G";
                    break;
                case 5:
                    state = @"WIFI";
                    break;
                default:
                    break;
            }
        }
    }

    return state;
}

////获得运营商
//- (NSString *)getMno {
//    NSString *mno = @"无";
//    CTTelephonyNetworkInfo *info = [[CTTelephonyNetworkInfo alloc]init];
//    CTCarrier *carrier = info.subscriberCellularProvider;
//    NSString *carrierName = carrier.carrierName;
//    NSString *mobileCountryCode = carrier.mobileCountryCode;
//    NSString *mobileNetworkCode = carrier.mobileNetworkCode;
//    NSInteger NetworkCode = 460;  // 中国
//    if ([mobileCountryCode intValue] == NetworkCode) {
//        if ([carrierName rangeOfString:@"联通"].length>0 || [mobileNetworkCode isEqualToString:@"01"] || [mobileNetworkCode isEqualToString:@"06"] || [mobileNetworkCode isEqualToString:@"09"]) {
//            mno = @"联通";
//        } else if ([carrierName rangeOfString:@"移动"].length>0 || [mobileNetworkCode isEqualToString:@"00"] || [mobileNetworkCode isEqualToString:@"02"] || [mobileNetworkCode isEqualToString:@"07"]){
//            mno = @"移动";
//        } else if ([carrierName rangeOfString:@"电信"].length>0 || [mobileNetworkCode isEqualToString:@"03"] || [mobileNetworkCode isEqualToString:@"05"] || [mobileNetworkCode isEqualToString:@"11"]){
//            mno = @"电信";
//        } else if ([carrierName rangeOfString:@"铁通"].length>0 || [mobileNetworkCode isEqualToString:@"20"]){
//            mno = @"铁通";
//        }
//    }
//    return mno;
//}


@end
