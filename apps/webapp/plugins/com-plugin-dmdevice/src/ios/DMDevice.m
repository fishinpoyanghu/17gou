/********* DMDevice.m Cordova Plugin Implementation *******/

#import <Cordova/CDV.h>

@interface DMDevice : CDVPlugin {
  // Member variables go here.
}

- (void)echo:(CDVInvokedUrlCommand*)command;
- (void)checkApp:(CDVInvokedUrlCommand*)command;

@end

@implementation DMDevice

- (void)echo:(CDVInvokedUrlCommand*)command {
    CDVPluginResult* pluginResult = nil;
    NSString* echo = [command.arguments objectAtIndex:0];

    if (echo != nil && [echo length] > 0) {
        pluginResult = [CDVPluginResult resultWithStatus:CDVCommandStatus_OK messageAsString:echo];
    } else {
        pluginResult = [CDVPluginResult resultWithStatus:CDVCommandStatus_ERROR];
    }

    [self.commandDelegate sendPluginResult:pluginResult callbackId:command.callbackId];
}

- (void)checkApp:(CDVInvokedUrlCommand*)command {
    NSString *type = [command.arguments objectAtIndex:0];
    if([type isEqualToString:@"wechat"]) {
        type = @"weixin://";
    }
    
    CDVPluginResult* pluginResult = nil;
    if ([[UIApplication sharedApplication] canOpenURL:[NSURL URLWithString:type]]) {
        pluginResult = [CDVPluginResult resultWithStatus:CDVCommandStatus_OK messageAsString:@"应用已安装"];
    } else {
        pluginResult = [CDVPluginResult resultWithStatus:CDVCommandStatus_ERROR];
    }
    [self.commandDelegate sendPluginResult:pluginResult callbackId:command.callbackId];
}

@end
