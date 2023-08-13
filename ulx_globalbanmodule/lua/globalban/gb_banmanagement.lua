--ULX Global Ban
--Adobe And NigNog and hounddog
------------------
include('globalban/gb_config.lua')
------------------

-- Overwrite The Ulib Function on a global scope
function ULib.addBan( steamid, time, reason, name, admin )
	-- No SteamID / Time, stop the script
	if steamid == nil then print('ERROR: [ULX GB] no steam id, contact dev.')return end
	if time == nil then return print('ERROR: [ULX GB] no time for ban found, contact dev.') end

	-- No Name!? Insert a false one
	if (name == nil) then
		if GB_NoSteamName == true then
			name = GB_BanName
		end
	end

	-- Get ban Length and add it os.time
	local BanLength = 0;
	if time == 0 then
		BanLength = 0;
	else
		BanLength = tonumber(os.time()) + (tonumber(time) * 60)
	end

	--Setup Admin Information
	local AdminName = "CONSOLE";
	local AdminSteam = "CONSOLE";
	if admin != nil && admin:IsPlayer() then
		AdminName = admin:Nick()
		AdminSteam = admin:SteamID()
	end

	--Are they already banned?
	local BanStatus = ULX_DB:prepare("SELECT BanID, Length FROM bans WHERE OSteamiD = ? LIMIT 1;")
	BanStatus:setString( 1, steamid )
	function BanStatus.onSuccess()
		local data = BanStatus:getData()
		local row = data[1]
		PrintTable(data)
		if (#data >= 1) then -- if result is found
			if name == GB_BanName then
				name = nil
			end
			GB_ModifyBan(name, BanLength, reason, time, AdminName, steamid)
		end
		if (#data == 0) then
			GB_InsertBan(steamid, name, BanLength, AdminName, AdminSteam, reason)
		end
	end
	function BanStatus.onError( err, sql ) print('[ULX GB] (BanStatus) - Error: ', err) end

	BanStatus:start()

	--Refresh the List!
	ULib.refreshBans()
end

function GB_InsertBan(steamid, name, BanLength, AdminName, AdminSteam, reason)
	local nm = name
	if (nm == nil) then
		nm = 'NULL'
	end

	--Insert Ban
	-- local String = "INSERT INTO bans VALUES ('','"..steamid.."','"..GB_Escape(name).."','"..BanLength.."','"..os.time().."',
	-- '"..GB_Escape(AdminName).."','"..AdminSteam.."','"..GB_Escape(reason).."','"..GB_SERVERID.."','','"..os.time().."');"
	local String = "INSERT INTO bans (OSteamID, OName, Length, Time, AName, ASteamID, Reason, ServerID, MAdmin, MTime) VALUES (?,?,?,?,?,?,?,?,?,?);"
	
	-- if name == nil then
		-- String = "INSERT INTO bans VALUES ('','"..steamid.."',NULL,'"..BanLength.."','"..os.time().."','"..GB_Escape(AdminName).."','"..AdminSteam.."','"..GB_Escape(reason).."','"..GB_SERVERID.."','','"..os.time().."');"
	-- 	name = 'NULL'
	-- end
	if String then print(String) end
	-- local AddBanQuery = ULX_DB:query(String)
	local AddBanQuery = ULX_DB:prepare(String)
	AddBanQuery:setString( 1, steamid )
	AddBanQuery:setString( 2, GB_Escape(nm) )
	AddBanQuery:setNumber( 3, BanLength )
	AddBanQuery:setNumber( 4, os.time() )
	AddBanQuery:setString( 5, GB_Escape(AdminName) )
	AddBanQuery:setString( 6, AdminSteam )
	AddBanQuery:setString( 7, GB_Escape(reason) )
	AddBanQuery:setNumber( 8, GB_SERVERID )
	AddBanQuery:setString( 9, '' )
	AddBanQuery:setNumber( 10, os.time() )
	function AddBanQuery.onSuccess()
		print("[ULX GB] - Ban Added!");
		if name == nil then
			ULib.bans[steamid] = { unban = tonumber(BanLength), admin = AdminName, reason = reason, time = tonumber(os.time()), modified_admin = '', modified_time = tonumber(0) };
		else
			ULib.bans[steamid] = { unban = tonumber(BanLength), admin = AdminName, reason = reason,name = name, time = tonumber(os.time()), modified_admin = '', modified_time = tonumber(0) };
		end
	end

	function AddBanQuery.onError(err, sql)
		print('[ULX GB] (AddBanQuery) - Error: ', err)
		-- fixed so corrent mysql syntax is working
		local nm = name
		if nm == nil then
			nm = 'NULL'
		end
		GB_AddTField("INSERT INTO bans (OSteamID, OName, Length, Time, AName, ASteamID, Reason, ServerID, MAdmin, MTime) VALUES ('"..steamid.."','"..GB_Escape(name).."','"..BanLength.."','"..os.time().."','"..GB_Escape(AdminName).."','"..AdminSteam.."','"..GB_Escape(reason).."','"..GB_SERVERID.."','','"..os.time().."');")
	end
	AddBanQuery:start()

	-- Regardless of outcome Kick player From Server
	RunConsoleCommand('kickid',steamid,"You've been banned from the server.");
end

function GB_ModifyBan(name, BanLength, reason, time, AdminName, steamid)
	--Send ban update to the Database
	-- local UpdateBanQuery = ULX_DB:query("UPDATE bans SET OName='".. GB_Escape(name) .."', Length='".. BanLength .."', Reason='".. GB_Escape(reason) .."',
	-- MTime='".. time .."', MAdmin='".. GB_Escape(AdminName) .."' WHERE OSteamID='".. steamid .."';");
	local UpdateBanQuery = ULX_DB:prepare("UPDATE bans SET OName=?, Length=?, Reason=?, MTime=?, MAdmin=? WHERE OSteamID=?;");
	UpdateBanQuery:setString( 1, GB_Escape(name) )
	UpdateBanQuery:setNumber( 2, BanLength )
	UpdateBanQuery:setString( 3, GB_Escape(reason) )
	UpdateBanQuery:setNumber( 4, os.time() )
	UpdateBanQuery:setString( 5, GB_Escape(AdminName) )
	UpdateBanQuery:setString( 6, steamid )
	function UpdateBanQuery.onSuccess()
		
		if name == nil then
			print("[ULX GB] - ".."Unbanning ID "..steamid..", automatically");
			ULib.bans[steamid] = { unban = tonumber(BanLength), admin = AdminName, reason = reason, modified_admin = GB_Escape(AdminName), modified_time = tonumber(time) };
		else
			print("[ULX GB] - ".."Unbanning ID "..steamid..", automatically, their name is NULL?");
			ULib.bans[steamid] = { unban = tonumber(BanLength), name = name, admin = AdminName, reason = reason, modified_admin = GB_Escape(AdminName), modified_time = tonumber(time) };
		end
	end
	function UpdateBanQuery.onError(err, sql)
		print('[ULX GB] (UpdateBanQuery) - Error: ', err)
		GB_AddTField("UPDATE bans SET OName='".. GB_Escape(name) .."', Length='".. BanLength .."', Reason='".. GB_Escape(reason) .."', MTime='".. time .."', MAdmin='".. GB_Escape(AdminName) .."' WHERE OSteamID='".. steamid .."';")
	end
	UpdateBanQuery:start()
end


-- Overwrite the ULib function for unbanning
function ULib.unban( steamid )
	--Query the Ban to the Database
	-- local UnBanQuery = ULX_DB:query("DELETE FROM bans WHERE OSteamID='"..steamid.."'");
	local UnBanQuery = ULX_DB:prepare("DELETE FROM bans WHERE OSteamID=?");
	UnBanQuery:setString( 1, steamid )
	function UnBanQuery.onSuccess()
		print(os.date("%m/%d/%y %H:%M").."[ULX GB] - Ban Expired for "..steamid..".");
		ULib.bans[steamid] = nil;
	end
	function UnBanQuery.onError(err, sql)
		print('[ULX GB] (UnBanQuery) - Error: ', err)
		GB_AddTField("DELETE FROM bans WHERE OSteamID='"..steamid.."'")
	end
	UnBanQuery:start()

	-- Possible Glitch Fix, Just Incase
	-- yeah idk either
	RunConsoleCommand('removeid',steamid);

	--Refresh the List!
	ULib.refreshBans()
end


-- Refreshes the ban List
function ULib.refreshBans()

	--Use their tables ;)
	ULib.bans = nil
	ULib.bans = {}
	xgui.ulxbans = {}

	local BanList = ULX_DB:query("SELECT * FROM bans ORDER BY BanID DESC")

	if !BanList then print('ERROR: Unable to communicate with the DB?')return end -- Fix Error when MySQL Server failure

	function BanList:onSuccess( data )
		for i = 1, #data do
			if data[i]['OName'] != nil then
				table.insert( ULib.bans, tonumber(data[i]['OSteamID']) )ULib.bans[data[i]['OSteamID']] = { unban = tonumber(data[i]['Length']), admin = data[i]['AName'], reason = data[i]['Reason'], name = data[i]['OName'], time = tonumber(data[i]['Time']), modified_admin = data[i]['MAdmin'], modified_time = tonumber(data[i]['MTime']) }
			else
				table.insert( ULib.bans, tonumber(data[i]['OSteamID']) )ULib.bans[data[i]['OSteamID']] = { unban = tonumber(data[i]['Length']), admin = data[i]['AName'], reason = data[i]['Reason'], time = tonumber(data[i]['Time']), modified_admin = data[i]['MAdmin'], modified_time = tonumber(data[i]['MTime']) }
			end
			--^^ ULX Ban Info
			---------------------------------
			for k, v in pairs( ULib.bans ) do
				xgui.ulxbans[k] = v           -- Make sure it loads bans!
			end
			---------------ok-----------------
			local t = {}
			t[data[i]['OSteamID']] = ULib.bans[data[i]['OSteamID']]
			xgui.addData( {}, "bans", t ) -- This will error out on startup (Most Times, GMod 13's Addon Loading is fucked), but that's fine, all ban data gets loaded already
		end

		if GB_UsageStats then
			GB_SendUsageStats(#data);
		end
	end
	function BanList.onError(err, sql) print('[ULX GB] (BanList) - Error: ', err) end
	BanList:start()

end
-- Refresh on Script Load -- Otherwise has issues
ULib.refreshBans()


local banJoinMsg = [[
===========YOU ARE BANNED===========
By: %s
Reason: %s
Banned since: %s
Time Left: %s

If you wish to appeal your ban, please appeal at darkstorm.cc/appeal

]]
local function checkBan( steamid64, ip, password, clpassword, name )
	
	
	
    local steamid = GB_ComIDtoSteamID(steamid64)
	print('[ULX GB] checking '..steamid..' '..ip..' '..name) 
    local banData = ULib.bans[ steamid ]
	if banData then
		for k,v in pairs(banData) do
			print(v)
		end
		print('time '..banData.time)
	end
	
    print("[ULX GB] AUTHING PLAYER: " .. name .. ' WITH SteamID: ' .. steamid)
    if not banData then
		 return print('[ULX GB] No ban found for '..steamid..'.') 
	end -- Not banned

    local admin = "Console"
    if banData.admin and banData.admin ~= "" then
        admin = banData.admin
    end

    local reason = "(None given)"
    if banData.reason and banData.reason ~= "" then
        reason = banData.reason
    end

    local timeban = "Unknown"
    if banData.time > 0 then
        timeban = os.date("%m/%d/%y %H:%M", timeban.time)
		--print('aftermod'..timeban)
    end

    local unbanStr = nil
	
    local unban = tonumber( banData.unban )

    if unban and unban > 0 then -- if unban time exists and is > 0 
		
        unbanStr = ULib.secondsToStringTime( unban - os.time() )
		--print("time test ="..unbanStr) PASSED
    end
	
	local bantime = banData.unban
	--print('bantime'..bantime)
    if (bantime <= os.time() && bantime != 0) then
			print("[ULX GB] - Removing expired bans!");
			ULib.unban(steamid);
	elseif reason or unbanStr then -- Only give this message if we have something useful to display
        Msg(string.format("[ULX GB] ".."%s (%s)<%s> was kicked because they are on the ban list\n", name, steamid, ip))
        return false, banJoinMsg:format( admin, reason, timeban, unbanStr or "(Permaban)" )
    end
	
end
hook.Add( "CheckPassword", "CheckPassword_GB", checkBan )


// Timer
timer.Create( "GB_RefreshTimer", GB_RefreshTime, 0, function() ULib.refreshBans() end)
